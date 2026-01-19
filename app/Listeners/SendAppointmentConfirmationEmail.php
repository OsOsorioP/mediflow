<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\AppointmentCreated;
use App\Events\AppointmentConfirmed;
use App\Jobs\SendAppointmentConfirmation;

class SendAppointmentConfirmationEmail
{

    public function __construct()
    {
        //
    }

    public function handle(AppointmentCreated|AppointmentConfirmed $event): void
    {

        SendAppointmentConfirmation::dispatch($event->appointment)
            ->delay(now()->addSeconds(5));
    }
}