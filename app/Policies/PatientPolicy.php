<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{

    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    public function view(User $user, Patient $patient): bool
    {
        if ($user->clinic_id !== $patient->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    public function create(User $user): bool
    {
        if (!$user->clinic->canAddPatient()) {
            return false;
        }

        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    public function update(User $user, Patient $patient): bool
    {
        if ($user->clinic_id !== $patient->clinic_id) {
            return false;
        }

        if (!$patient->is_active && $user->role !== UserRole::ADMIN) {
            return false;
        }

        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    public function delete(User $user, Patient $patient): bool
    {
        if ($user->clinic_id !== $patient->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN;
    }

    public function restore(User $user, Patient $patient): bool
    {
        if ($user->clinic_id !== $patient->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN;
    }

    public function archive(User $user, Patient $patient): bool
    {
        if ($user->clinic_id !== $patient->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN;
    }

    public function viewMedicalRecords(User $user, Patient $patient): bool
    {
        if ($user->clinic_id !== $patient->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }
}