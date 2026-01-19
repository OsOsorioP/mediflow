<?php

declare(strict_types=1);

namespace App\Actions\Appointments;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Models\WorkingHours;
use App\Services\TenantManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ScheduleAppointmentAction
{
    public function execute(array $data): Appointment
    {
        $scheduledAt = Carbon::parse($data['scheduled_at']);
        $duration = (int) $data['duration_minutes'];
        $doctorId = (int) $data['user_id'];
        $patientId = (int) $data['patient_id'];

        $clinicId = app(TenantManager::class)->getClinicId();

        if (!$clinicId) {
            throw new \Exception("No se puede agendar una cita sin un contexto de clínica activo.");
        }

        return DB::transaction(function () use ($data, $scheduledAt, $duration, $doctorId, $patientId, $clinicId) {
            if ($scheduledAt->isPast()) {
                throw ValidationException::withMessages([
                    'scheduled_at' => 'No se pueden agendar citas en el pasado.',
                ]);
            }

            $this->validateWorkingHours($clinicId, $scheduledAt, $duration);

            $this->validateDoctorAvailability($doctorId, $scheduledAt, $duration);

            $this->validatePatientAvailability($patientId, $scheduledAt, $duration);

            $appointment = Appointment::create([
                'clinic_id' => $clinicId,
                'patient_id' => $patientId,
                'user_id' => $doctorId,
                'scheduled_at' => $scheduledAt,
                'duration_minutes' => $duration,
                'status' => AppointmentStatus::PENDING,
                'appointment_type' => $data['appointment_type'] ?? null,
                'reason' => $data['reason'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            event(new \App\Events\AppointmentCreated($appointment));

            return $appointment;
        });
    }

    protected function validateWorkingHours(int $clinicId, Carbon $scheduledAt, int $duration): void
    {
        $dayOfWeek = $scheduledAt->dayOfWeek;

        $workingHours = WorkingHours::query()
            ->where('clinic_id', $clinicId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$workingHours) {
            throw ValidationException::withMessages([
                'scheduled_at' => 'No hay horario de atención configurado para este día.',
            ]);
        }

        $appointmentStart = $scheduledAt->format('H:i:s');
        $appointmentEnd = $scheduledAt->copy()->addMinutes($duration)->format('H:i:s');

        $workingStart = $workingHours->start_time->format('H:i:s');
        $workingEnd = $workingHours->end_time->format('H:i:s');

        if ($appointmentStart < $workingStart || $appointmentEnd > $workingEnd) {
            throw ValidationException::withMessages([
                'scheduled_at' => "El horario debe estar dentro del rango permitido: {$workingStart} - {$workingEnd}.",
            ]);
        }
    }

    protected function validateDoctorAvailability(int $doctorId, Carbon $scheduledAt, int $duration): void
    {
        $endTime = $scheduledAt->copy()->addMinutes($duration);

        $conflictingAppointment = Appointment::query()
            ->where('user_id', $doctorId)
            ->whereIn('status', AppointmentStatus::activeStatuses())
            ->where('scheduled_at', '<', $endTime) // Inicio de la existente antes de que termine la nueva
            ->whereRaw("(scheduled_at + (duration_minutes || ' minutes')::interval) > ?", [$scheduledAt]) // Fin de la existente después de que inicie la nueva
            ->lockForUpdate()
            ->first();

        if ($conflictingAppointment) {
            $doctor = User::find($doctorId);
            throw ValidationException::withMessages([
                'scheduled_at' => "El Dr. " . ($doctor->name ?? 'seleccionado') . " ya tiene una cita agendada en ese horario.",
            ]);
        }
    }

    protected function validatePatientAvailability(int $patientId, Carbon $scheduledAt, int $duration): void
    {
        $endTime = $scheduledAt->copy()->addMinutes($duration);

        $hasConflict = Appointment::query()
            ->where('patient_id', $patientId)
            ->whereIn('status', AppointmentStatus::activeStatuses())
            ->where(function ($query) use ($scheduledAt, $endTime) {
                $query->whereBetween('scheduled_at', [$scheduledAt, $endTime->copy()->subSecond()])
                    ->orWhereRaw("(scheduled_at + (duration_minutes || ' minutes')::interval) > ?", [$scheduledAt])
                    ->where('scheduled_at', '<', $endTime);
            })
            ->lockForUpdate()
            ->first();

        if ($hasConflict) {
            $patient = Patient::find($patientId);
            throw ValidationException::withMessages([
                'patient_id' => "El paciente " . ($patient->full_name ?? '') . " ya tiene una cita en ese horario.",
            ]);
        }
    }

    public function getAvailableSlots(int $doctorId, Carbon $date, int $slotDuration = 30): array
    {
        $clinicId = app(TenantManager::class)->getClinicId();
        $dayOfWeek = $date->dayOfWeek;

        if (!$clinicId) {
            return [];
        }

        $workingHours = WorkingHours::query()
            ->where('clinic_id', $clinicId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$workingHours) {
            return [];
        }

        $allSlots = $workingHours->getAvailableSlots($slotDuration);

        $existingAppointments = Appointment::query()
            ->where('user_id', $doctorId)
            ->whereIn('status', AppointmentStatus::activeStatuses())
            ->whereDate('scheduled_at', $date)
            ->get();

        $availableSlots = [];
        foreach ($allSlots as $slot) {
            $slotTime = Carbon::parse($date->format('Y-m-d') . ' ' . $slot);
            $slotEnd = $slotTime->copy()->addMinutes($slotDuration);

            $isAvailable = true;
            foreach ($existingAppointments as $appointment) {
                if ($slotTime->lt($appointment->end_time) && $slotEnd->gt($appointment->scheduled_at)) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable) {
                $availableSlots[] = $slot;
            }
        }

        return $availableSlots;
    }
}
