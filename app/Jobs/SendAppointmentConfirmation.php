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

class SendAppointmentConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(
        public Appointment $appointment
    ) {
        //
    }

    public function handle(): void
    {
        if (!$this->appointment->patient->email) {
            return;
        }

        if (!$this->appointment->status->isActive()) {
            return;
        }

        Mail::to($this->appointment->patient->email)
            ->send(new AppointmentConfirmation($this->appointment));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Error enviando confirmación de cita', [
            'appointment_id' => $this->appointment->id,
            'patient_email' => $this->appointment->patient->email,
            'error' => $exception->getMessage(),
        ]);
    }
}
