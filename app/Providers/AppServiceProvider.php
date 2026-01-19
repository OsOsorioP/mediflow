<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\TenantManager;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TenantManager::class);
    }

    public function boot(): void
    {
        Gate::before(function (User $user, string $ability) {

            if (! $user->is_active) {
                return false;
            }

            if ($user->isAdmin() && $ability !== 'update-profile') {
                return true;
            }

            return null;
        });

        Gate::define('manage-users', function (User $user) {
            return $user->role === UserRole::ADMIN;
        });

        Gate::define('manage-clinic-settings', function (User $user) {
            return $user->role === UserRole::ADMIN;
        });

        Gate::define('view-financial-reports', function (User $user) {
            return $user->role === UserRole::ADMIN;
        });

        Gate::define('manage-patients', function (User $user) {
            return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
        });

        Gate::define('manage-appointments', function (User $user) {
            return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
        });

        Gate::define('update-profile', function (User $user, User $targetUser) {
            return $user->id === $targetUser->id;
        });

        Gate::before(function (User $user, string $ability) {
            if ($user->role === UserRole::ADMIN && $ability !== 'update-profile') {
                return true;
            }
        });
    }
}
