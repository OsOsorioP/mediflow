<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->role === UserRole::ADMIN;
    }

    public function create(User $user): bool
    {
        if ($user->role !== UserRole::ADMIN) {
            return false;
        }

        return $user->clinic->canAddUser();
    }

    public function update(User $user, User $model): bool
    {
        if ($user->clinic_id !== $model->clinic_id) {
            return false;
        }

        if ($user->id === $model->id) {
            return true;
        }

        return $user->role === UserRole::ADMIN;
    }

    public function delete(User $user, User $model): bool
    {

        if ($user->id === $model->id) {
            return false;
        }


        if ($user->clinic_id !== $model->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN;
    }

    public function restore(User $user, User $model): bool
    {
        return $user->role === UserRole::ADMIN
            && $user->clinic_id === $model->clinic_id;
    }

    public function changeRole(User $user, User $model): bool
    {
        if ($user->role !== UserRole::ADMIN) {
            return false;
        }

        if ($user->id === $model->id) {
            return false;
        }

        return $user->clinic_id === $model->clinic_id;
    }
}
