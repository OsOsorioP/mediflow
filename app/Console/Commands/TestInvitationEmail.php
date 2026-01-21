<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\UserInvitation;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestInvitationEmail extends Command
{
    protected $signature = 'test:invitation-email {email=test@example.com}';

    protected $description = 'Envía un email de prueba de invitación';

    public function handle(): int
    {
        $clinic = new Clinic([
            'name' => 'Clínica de Prueba',
        ]);

        $user = new User([
            'name' => 'Dr. Juan Pérez',
            'email' => $this->argument('email'),
            'role' => \App\Enums\UserRole::ADMIN,
        ]);

        $temporaryPassword = 'TempPass123!';

        Mail::to($this->argument('email'))
            ->send(new UserInvitation($user, $temporaryPassword, $clinic));

        $this->info("✅ Email enviado a: {$this->argument('email')}");
        $this->info("📬 Revisa Mailpit en: http://localhost:8025");

        return Command::SUCCESS;
    }
}