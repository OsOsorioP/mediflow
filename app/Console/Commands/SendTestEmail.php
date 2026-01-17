<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\AppointmentConfirmation;
use App\Mail\AppointmentReminder;
use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'email:test {type=confirmation} {appointment_id?}';

    /**
     * The console command description.
     */
    protected $description = 'Enviar un email de prueba';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->argument('type');
        $appointmentId = $this->argument('appointment_id');

        // Obtener una cita (la primera si no se especifica)
        $appointment = $appointmentId 
            ? Appointment::findOrFail($appointmentId)
            : Appointment::with(['patient', 'doctor', 'clinic'])->first();

        if (!$appointment) {
            $this->error('No hay citas en la base de datos');
            return self::FAILURE;
        }

        if (!$appointment->patient->email) {
            $this->error('El paciente no tiene email configurado');
            return self::FAILURE;
        }

        $this->info("Enviando email de tipo '{$type}' a {$appointment->patient->email}...");

        try {
            $mailable = match($type) {
                'confirmation' => new AppointmentConfirmation($appointment),
                'reminder' => new AppointmentReminder($appointment),
                default => throw new \InvalidArgumentException("Tipo de email inválido: {$type}")
            };

            Mail::to($appointment->patient->email)->send($mailable);

            $this->info('✅ Email enviado correctamente');
            $this->info("📧 Verifica Mailpit en http://localhost:8025");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Error: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}