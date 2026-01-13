<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    /**
     * Determina si el usuario puede ver la lista de pacientes
     */
    public function viewAny(User $user): bool
    {
        // Tanto admins como asistentes pueden ver pacientes
        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    /**
     * Determina si el usuario puede ver un paciente específico
     */
    public function view(User $user, Patient $patient): bool
    {
        // Validación de tenant: Solo puede ver pacientes de su clínica
        if ($user->clinic_id !== $patient->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    /**
     * Determina si el usuario puede crear pacientes
     */
    public function create(User $user): bool
    {
        // Verificar límite de pacientes de la clínica
        if (!$user->clinic->canAddPatient()) {
            return false;
        }

        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    /**
     * Determina si el usuario puede actualizar un paciente
     */
    public function update(User $user, Patient $patient): bool
    {
        // Validación de tenant
        if ($user->clinic_id !== $patient->clinic_id) {
            return false;
        }

        // No se pueden editar pacientes inactivos (archivados)
        if (!$patient->is_active && $user->role !== UserRole::ADMIN) {
            return false;
        }

        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    /**
     * Determina si el usuario puede eliminar un paciente
     */
    public function delete(User $user, Patient $patient): bool
    {
        // Validación de tenant
        if ($user->clinic_id !== $patient->clinic_id) {
            return false;
        }

        // Solo admins pueden eliminar pacientes
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determina si el usuario puede restaurar un paciente eliminado
     */
    public function restore(User $user, Patient $patient): bool
    {
        if ($user->clinic_id !== $patient->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determina si el usuario puede archivar/desarchivar un paciente
     */
    public function archive(User $user, Patient $patient): bool
    {
        if ($user->clinic_id !== $patient->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determina si el usuario puede acceder al expediente médico
     */
    public function viewMedicalRecords(User $user, Patient $patient): bool
    {
        if ($user->clinic_id !== $patient->clinic_id) {
            return false;
        }

        // Tanto admin como asistente pueden ver expedientes
        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }
}