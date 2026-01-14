<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Policies\UserPolicy;
use App\Policies\PatientPolicy;
use APP\Policies\MedicalRecordPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Patient::class => PatientPolicy::class,
        MedicalRecord::class => MedicalRecordPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
