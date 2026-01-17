<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\AppointmentCreated;
use App\Events\AppointmentConfirmed;
use App\Jobs\SendAppointmentConfirmation;

class SendAppointmentConfirmationEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AppointmentCreated|AppointmentConfirmed $event): void
    {
        // Despachar el job a la cola para envío asíncrono
        SendAppointmentConfirmation::dispatch($event->appointment)
            ->delay(now()->addSeconds(5)); // Pequeño delay para que la transacción DB se complete
    }
}