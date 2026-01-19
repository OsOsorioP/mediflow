<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\MedicalRecord;
use App\Models\User;

class MedicalRecordPolicy
{

    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }


    public function view(User $user, MedicalRecord $medicalRecord): bool
    {

        if ($user->clinic_id !== $medicalRecord->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }


    public function create(User $user): bool
    {
        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }


    public function update(User $user, MedicalRecord $medicalRecord): bool
    {

        if ($user->clinic_id !== $medicalRecord->clinic_id) {
            return false;
        }

        if ($medicalRecord->created_by === $user->id) {
            return true;
        }

        return $user->role === UserRole::ADMIN;
    }

    public function delete(User $user, MedicalRecord $medicalRecord): bool
    {

        if ($user->clinic_id !== $medicalRecord->clinic_id) {
            return false;
        }


        return $user->role === UserRole::ADMIN;
    }

    public function restore(User $user, MedicalRecord $medicalRecord): bool
    {
        if ($user->clinic_id !== $medicalRecord->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN;
    }

    public function forceDelete(User $user, MedicalRecord $medicalRecord): bool
    {
        return false;
    }

    public function export(User $user, MedicalRecord $medicalRecord): bool
    {
        if ($user->clinic_id !== $medicalRecord->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }
}