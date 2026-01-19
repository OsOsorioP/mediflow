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

class SendAppointmentReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function handle(): void
    {
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
