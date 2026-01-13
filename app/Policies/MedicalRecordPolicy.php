<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\MedicalRecord;
use App\Models\User;

class MedicalRecordPolicy
{
    /**
     * Determina si el usuario puede ver la lista de registros médicos
     */
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    /**
     * Determina si el usuario puede ver un registro médico específico
     */
    public function view(User $user, MedicalRecord $medicalRecord): bool
    {
        // Validación de tenant
        if ($user->clinic_id !== $medicalRecord->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    /**
     * Determina si el usuario puede crear registros médicos
     */
    public function create(User $user): bool
    {
        // Tanto admins como asistentes pueden crear registros
        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    /**
     * Determina si el usuario puede actualizar un registro médico
     */
    public function update(User $user, MedicalRecord $medicalRecord): bool
    {
        // Validación de tenant
        if ($user->clinic_id !== $medicalRecord->clinic_id) {
            return false;
        }

        // Solo el creador del registro o un admin pueden editarlo
        if ($medicalRecord->created_by === $user->id) {
            return true;
        }

        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determina si el usuario puede eliminar un registro médico
     */
    public function delete(User $user, MedicalRecord $medicalRecord): bool
    {
        // Validación de tenant
        if ($user->clinic_id !== $medicalRecord->clinic_id) {
            return false;
        }

        // Solo admins pueden eliminar registros médicos
        // (En producción, podrías querer deshabilitar completamente el delete)
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determina si el usuario puede restaurar un registro eliminado
     */
    public function restore(User $user, MedicalRecord $medicalRecord): bool
    {
        if ($user->clinic_id !== $medicalRecord->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN;
    }

    /**
     * Prevenir eliminación permanente (force delete)
     * En sistemas médicos, NUNCA debe permitirse
     */
    public function forceDelete(User $user, MedicalRecord $medicalRecord): bool
    {
        // Siempre false - nunca permitir eliminación física
        return false;
    }

    /**
     * Determina si el usuario puede exportar el registro (PDF, etc.)
     */
    public function export(User $user, MedicalRecord $medicalRecord): bool
    {
        if ($user->clinic_id !== $medicalRecord->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }
}