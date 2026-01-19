<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{

    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->clinic_id !== $appointment->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->clinic_id !== $appointment->clinic_id) {
            return false;
        }

        if (!$appointment->canBeModified()) {
            return false;
        }

        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    public function cancel(User $user, Appointment $appointment): bool
    {
        if ($user->clinic_id !== $appointment->clinic_id) {
            return false;
        }

        if (!$appointment->canBeCancelled()) {
            return false;
        }

        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        if ($user->clinic_id !== $appointment->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN;
    }

    public function confirm(User $user, Appointment $appointment): bool
    {
        if ($user->clinic_id !== $appointment->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    public function complete(User $user, Appointment $appointment): bool
    {
        if ($user->clinic_id !== $appointment->clinic_id) {
            return false;
        }

        return $appointment->user_id === $user->id || $user->role === UserRole::ADMIN;
    }
}