<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\AppointmentConfirmed;
use App\Events\AppointmentCreated;
use App\Listeners\SendAppointmentConfirmationEmail;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AppointmentCreated::class => [
            SendAppointmentConfirmationEmail::class,
        ],
        AppointmentConfirmed::class => [
            SendAppointmentConfirmationEmail::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}