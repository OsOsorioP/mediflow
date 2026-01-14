<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    /**
     * Ver lista de citas
     */
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    /**
     * Ver una cita específica
     */
    public function view(User $user, Appointment $appointment): bool
    {
        // Validación de tenant
        if ($user->clinic_id !== $appointment->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    /**
     * Crear citas
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    /**
     * Actualizar citas
     */
    public function update(User $user, Appointment $appointment): bool
    {
        // Validación de tenant
        if ($user->clinic_id !== $appointment->clinic_id) {
            return false;
        }

        // No se pueden modificar citas pasadas o completadas
        if (!$appointment->canBeModified()) {
            return false;
        }

        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    /**
     * Cancelar citas
     */
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

    /**
     * Eliminar citas (soft delete)
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        if ($user->clinic_id !== $appointment->clinic_id) {
            return false;
        }

        // Solo admins pueden eliminar
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Confirmar citas
     */
    public function confirm(User $user, Appointment $appointment): bool
    {
        if ($user->clinic_id !== $appointment->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    /**
     * Completar citas
     */
    public function complete(User $user, Appointment $appointment): bool
    {
        if ($user->clinic_id !== $appointment->clinic_id) {
            return false;
        }

        // Solo el médico asignado o un admin pueden completar
        return $appointment->user_id === $user->id || $user->role === UserRole::ADMIN;
    }
}