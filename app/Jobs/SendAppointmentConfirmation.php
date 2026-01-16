<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\AppointmentConfirmation;
use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAppointmentConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de intentos antes de fallar
     */
    public int $tries = 3;

    /**
     * Timeout del job en segundos
     */
    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Appointment $appointment
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Verificar que el paciente tenga email
        if (!$this->appointment->patient->email) {
            return;
        }

        // Verificar que la cita siga activa
        if (!$this->appointment->status->isActive()) {
            return;
        }

        Mail::to($this->appointment->patient->email)
            ->send(new AppointmentConfirmation($this->appointment));
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Log del error
        \Log::error('Error enviando confirmación de cita', [
            'appointment_id' => $this->appointment->id,
            'patient_email' => $this->appointment->patient->email,
            'error' => $exception->getMessage(),
        ]);
    }
}