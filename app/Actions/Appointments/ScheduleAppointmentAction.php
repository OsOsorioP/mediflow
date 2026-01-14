<?php

declare(strict_types=1);

namespace App\Actions\Appointments;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Models\WorkingHours;
use App\Services\TenantManager; // Importar nuestro Manager
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; // Importar Facade
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ScheduleAppointmentAction
{
    /**
     * Agenda una cita con validaciones robustas y prevención de double-booking
     */
    public function execute(array $data): Appointment
    {
        // Normalizar datos
        $scheduledAt = Carbon::parse($data['scheduled_at']);
        $duration = (int) $data['duration_minutes'];
        $doctorId = (int) $data['user_id'];
        $patientId = (int) $data['patient_id'];
        
        // Obtenemos el clinic_id de forma segura a través del Manager
        $clinicId = app(TenantManager::class)->getClinicId();

        if (!$clinicId) {
            throw new \Exception("No se puede agendar una cita sin un contexto de clínica activo.");
        }

        return DB::transaction(function () use ($data, $scheduledAt, $duration, $doctorId, $patientId, $clinicId) {
            // 1. Validar que no sea una fecha pasada
            if ($scheduledAt->isPast()) {
                throw ValidationException::withMessages([
                    'scheduled_at' => 'No se pueden agendar citas en el pasado.',
                ]);
            }

            // 2. Validar horario de atención
            $this->validateWorkingHours($clinicId, $scheduledAt, $duration);

            // 3. Validar disponibilidad del médico (LOCK PESSIMISTIC)
            $this->validateDoctorAvailability($doctorId, $scheduledAt, $duration);

            // 4. Validar disponibilidad del paciente
            $this->validatePatientAvailability($patientId, $scheduledAt, $duration);

            // 5. Crear la cita (Línea 51 corregida)
            return Appointment::create([
                'clinic_id' => $clinicId,
                'patient_id' => $patientId,
                'user_id' => $doctorId,
                'scheduled_at' => $scheduledAt,
                'duration_minutes' => $duration,
                'status' => AppointmentStatus::PENDING,
                'appointment_type' => $data['appointment_type'] ?? null,
                'reason' => $data['reason'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(), // Línea 60 corregida usando Facade
            ]);
        });
    }

    /**
     * Valida que la hora esté dentro del horario de atención
     */
    protected function validateWorkingHours(int $clinicId, Carbon $scheduledAt, int $duration): void
    {
        $dayOfWeek = $scheduledAt->dayOfWeek;

        // Línea 71 corregida: Recibimos clinicId por parámetro para evitar auth()
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

        $appointmentStart = $scheduledAt->format('H:i');
        $appointmentEnd = $scheduledAt->copy()->addMinutes($duration)->format('H:i');
        
        // Asumiendo que start_time y end_time son casts de datetime/carbon en el modelo
        $workingStart = $workingHours->start_time->format('H:i');
        $workingEnd = $workingHours->end_time->format('H:i');

        if ($appointmentStart < $workingStart || $appointmentEnd > $workingEnd) {
            throw ValidationException::withMessages([
                'scheduled_at' => "El horario debe estar entre {$workingStart} y {$workingEnd}.",
            ]);
        }
    }

    /**
     * Valida que el médico esté disponible (sin conflictos)
     */
    protected function validateDoctorAvailability(int $doctorId, Carbon $scheduledAt, int $duration): void
    {
        $endTime = $scheduledAt->copy()->addMinutes($duration);

        $conflictingAppointment = Appointment::query()
            ->where('user_id', $doctorId)
            ->whereIn('status', AppointmentStatus::activeStatuses())
            ->where(function ($query) use ($scheduledAt, $endTime) {
                $query->where(function ($q) use ($scheduledAt, $endTime) {
                    $q->whereBetween('scheduled_at', [$scheduledAt, $endTime->copy()->subSecond()]);
                })
                ->orWhere(function ($q) use ($scheduledAt, $endTime) {
                    // Nota: PostgreSQL requiere sintaxis específica para intervalos
                    $q->whereRaw("(scheduled_at + (duration_minutes || ' minutes')::interval) > ?", [$scheduledAt])
                      ->where('scheduled_at', '<', $endTime);
                });
            })
            ->lockForUpdate()
            ->first();

        if ($conflictingAppointment) {
            $doctor = User::find($doctorId);
            throw ValidationException::withMessages([
                'scheduled_at' => "El Dr. " . ($doctor->name ?? 'seleccionado') . " ya tiene una cita en ese horario.",
            ]);
        }
    }

    /**
     * Valida que el paciente no tenga otra cita activa a la misma hora
     */
    protected function validatePatientAvailability(int $patientId, Carbon $scheduledAt, int $duration): void
    {
        $endTime = $scheduledAt->copy()->addMinutes($duration);

        $conflictingAppointment = Appointment::query()
            ->where('patient_id', $patientId)
            ->whereIn('status', AppointmentStatus::activeStatuses())
            ->where(function ($query) use ($scheduledAt, $endTime) {
                $query->whereBetween('scheduled_at', [$scheduledAt, $endTime->copy()->subSecond()])
                    ->orWhereRaw("(scheduled_at + (duration_minutes || ' minutes')::interval) > ?", [$scheduledAt])
                    ->where('scheduled_at', '<', $endTime);
            })
            ->lockForUpdate()
            ->first();

        if ($conflictingAppointment) {
            $patient = Patient::find($patientId);
            throw ValidationException::withMessages([
                'patient_id' => "El paciente " . ($patient->full_name ?? '') . " ya tiene una cita en ese horario.",
            ]);
        }
    }

    /**
     * Obtiene slots disponibles (Línea 169 corregida)
     */
    public function getAvailableSlots(int $doctorId, Carbon $date, int $slotDuration = 30): array
    {
        $clinicId = app(TenantManager::class)->getClinicId();

        if (!$clinicId) return [];

        $workingHours = WorkingHours::query()
            ->where('clinic_id', $clinicId)
            ->where('day_of_week', $date->dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$workingHours) return [];

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
                // Usamos la propiedad calculada end_time si existe en el modelo
                if ($slotTime->lt($appointment->scheduled_at->copy()->addMinutes($appointment->duration_minutes)) && 
                    $slotEnd->gt($appointment->scheduled_at)) {
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