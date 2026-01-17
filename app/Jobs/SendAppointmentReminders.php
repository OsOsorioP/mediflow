<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\AppointmentStatus;
use App\Mail\AppointmentReminder;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Envía recordatorios de citas para el día siguiente.
 * 
 * Este Job se ejecuta diariamente y busca todas las citas programadas
 * para el día de mañana que estén activas, enviando un recordatorio
 * por email a los pacientes.
 */
class SendAppointmentReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de intentos antes de fallar.
     * @var int
     */
    public int $tries = 3;

    /**
     * Timeout del job en segundos.
     * @var int
     */
    public int $timeout = 120;

    /**
     * Ejecuta el trabajo.
     * 
     * Busca citas de mañana, filtra por estado y existencia de email,
     * y envía los correos. También registra el resultado en los logs.
     */
    public function handle(): void
    {
        // Obtener citas de mañana que estén activas
        $tomorrow = Carbon::tomorrow();

        $appointments = Appointment::query()
            ->with(['patient', 'doctor', 'clinic'])
            ->whereDate('scheduled_at', $tomorrow)
            ->whereIn('status', AppointmentStatus::activeStatuses())
            ->whereHas('patient', function ($query) {
                $query->whereNotNull('email');
            })
            ->get();

        $sent = 0;
        $failed = 0;

        foreach ($appointments as $appointment) {
            try {
                Mail::to($appointment->patient->email)
                    ->send(new AppointmentReminder($appointment));

                $sent++;

                // Opcional: Marcar que se envió el recordatorio
                $appointment->update([
                    'notes' => ($appointment->notes ?? '') . "\n[Recordatorio enviado: " . now()->format('Y-m-d H:i') . "]"
                ]);
            } catch (\Exception $e) {
                $failed++;
                Log::error('Error enviando recordatorio', [
                    'appointment_id' => $appointment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Recordatorios enviados', [
            'total_appointments' => $appointments->count(),
            'sent' => $sent,
            'failed' => $failed,
        ]);
    }
}
