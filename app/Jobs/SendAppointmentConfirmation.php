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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Envía un correo de confirmación de cita al paciente.
 * 
 * Este Job se encarga de verificar que la cita siga activa y el paciente
 * tenga email antes de enviar la confirmación.
 */
class SendAppointmentConfirmation implements ShouldQueue
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
    public int $timeout = 60;

    /**
     * Crea una nueva instancia del trabajo.
     * 
     * @param Appointment $appointment La cita que se va a confirmar.
     */
    public function __construct(
        public Appointment $appointment
    ) {
        //
    }

    /**
     * Ejecuta el trabajo.
     * 
     * Verifica la validez de la cita y el email del paciente antes de enviar.
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
     * Maneja el fallo del trabajo.
     * 
     * @param \Throwable $exception La excepción que causó el fallo.
     */
    public function failed(\Throwable $exception): void
    {
        // Log del error
        Log::error('Error enviando confirmación de cita', [
            'appointment_id' => $this->appointment->id,
            'patient_email' => $this->appointment->patient->email,
            'error' => $exception->getMessage(),
        ]);
    }
}
