<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $temporaryPassword,
        public Clinic $clinic
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Invitación a {$this->clinic->name} - MediFlow",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.users.invitation',
            
            with: [
                'userName' => $this->user->name,
                'userEmail' => $this->user->email,
                'temporaryPassword' => $this->temporaryPassword,
                'clinicName' => $this->clinic->name,
                'loginUrl' => route('login'),
                'roleName' => $this->user->role->label(),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
