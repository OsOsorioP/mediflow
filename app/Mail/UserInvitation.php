<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $temporaryPassword,
        public Clinic $clinic
    ) {}

    public function build(): self
    {
        return $this->subject('Invitation to join ' . $this->clinic->name)
            ->markdown('emails.users.invitation');
    }
}
