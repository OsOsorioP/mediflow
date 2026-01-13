<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gate: Solo admins pueden gestionar usuarios
        Gate::define('manage-users', function (User $user) {
            return $user->role === UserRole::ADMIN;
        });

        // Gate: Solo admins pueden ver configuración de la clínica
        Gate::define('manage-clinic-settings', function (User $user) {
            return $user->role === UserRole::ADMIN;
        });

        // Gate: Solo admins pueden ver reportes financieros
        Gate::define('view-financial-reports', function (User $user) {
            return $user->role === UserRole::ADMIN;
        });

        // Gate: Ambos roles pueden gestionar pacientes
        Gate::define('manage-patients', function (User $user) {
            return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
        });

        // Gate: Ambos roles pueden gestionar citas
        Gate::define('manage-appointments', function (User $user) {
            return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
        });

        // Gate: Solo el mismo usuario puede editar su propio perfil
        Gate::define('update-profile', function (User $user, User $targetUser) {
            return $user->id === $targetUser->id;
        });

        // Super Gate: Verificar si el usuario es admin (útil para múltiples checks)
        Gate::before(function (User $user, string $ability) {
            // Los admins pueden hacer TODO (super user)
            // Excepto editar perfiles de otros usuarios directamente
            if ($user->role === UserRole::ADMIN && $ability !== 'update-profile') {
                return true;
            }
        });
    }
}
