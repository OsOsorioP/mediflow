<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\Appointment;
use App\Models\Payment;
use App\Policies\UserPolicy;
use App\Policies\PatientPolicy;
use App\Policies\MedicalRecordPolicy;
use App\Policies\AppointmentPolicy;
use App\Policies\PaymentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Patient::class => PatientPolicy::class,
        MedicalRecord::class => MedicalRecordPolicy::class,
        Appointment::class => AppointmentPolicy::class,
        Payment::class => PaymentPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
