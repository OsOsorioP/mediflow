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

/**
 * Clase encargada de la lógica de negocio para agendar citas médicas.
 * Asegura que no existan traslapes de horarios y que se respete la jornada laboral.
 */
class ScheduleAppointmentAction
{
    /**
     * Ejecuta el proceso de agendado de una cita.
     * 
     * @param array $data Datos de la cita (user_id, patient_id, scheduled_at, duration_minutes, etc).
     * @return Appointment El modelo de la cita creada.
     * @throws ValidationException Si falla alguna validación de disponibilidad.
     * @throws \Exception Si no hay contexto de clínica.
     */
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
            // 1. No permitir citas en el pasado
            if ($scheduledAt->isPast()) {
                throw ValidationException::withMessages([
                    'scheduled_at' => 'No se pueden agendar citas en el pasado.',
                ]);
            }

            // 2. Verificar que la clínica esté abierta en ese horario
            $this->validateWorkingHours($clinicId, $scheduledAt, $duration);

            // 3. Verificar que el médico no tenga otra cita (Bloqueo pesimista para evitar condiciones de carrera)
            $this->validateDoctorAvailability($doctorId, $scheduledAt, $duration);

            // 4. Verificar que el paciente no tenga otra cita activa
            $this->validatePatientAvailability($patientId, $scheduledAt, $duration);

            // 5. Persistir la cita en la base de datos
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

    /**
     * Valida si el horario solicitado entra dentro de la configuración de la clínica.
     * 
     * @param int $clinicId
     * @param Carbon $scheduledAt
     * @param int $duration
     */
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

    /**
     * Comprueba si el médico tiene huecos libres usando bloqueo de registros.
     * 
     * @param int $doctorId
     * @param Carbon $scheduledAt
     * @param int $duration
     */
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

    /**
     * Comprueba si el paciente tiene otra cita que choque.
     * 
     * @param int $patientId
     * @param Carbon $scheduledAt
     * @param int $duration
     */
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

    /**
     * Genera una lista de horas disponibles para un médico en una fecha específica.
     * 
     * @param int $doctorId ID del médico.
     * @param Carbon $date Fecha a consultar.
     * @param int $slotDuration Duración de cada espacio en minutos.
     * @return array Lista de strings con horas disponibles (H:i).
     */
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

        // Obtener todos los slots teóricos del día (método interno del modelo WorkingHours)
        $allSlots = $workingHours->getAvailableSlots($slotDuration);

        // Obtener citas ya agendadas para filtrar
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
