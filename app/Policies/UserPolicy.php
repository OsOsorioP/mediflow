<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    /**
     * Determina si el usuario puede ver la lista de usuarios
     */
    public function viewAny(User $user): bool
    {
        // Solo admins pueden ver la lista completa de usuarios
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determina si el usuario puede ver un usuario específico
     */
    public function view(User $user, User $model): bool
    {
        // Puede ver su propio perfil o si es admin
        return $user->id === $model->id || $user->role === UserRole::ADMIN;
    }

    /**
     * Determina si el usuario puede crear usuarios
     */
    public function create(User $user): bool
    {
        // Solo admins pueden crear usuarios
        if ($user->role !== UserRole::ADMIN) {
            return false;
        }

        // Verificar que no se exceda el límite de usuarios de la clínica
        return $user->clinic->canAddUser();
    }

    /**
     * Determina si el usuario puede actualizar un usuario
     */
    public function update(User $user, User $model): bool
    {
        // Validación de tenant: Solo puede editar usuarios de su misma clínica
        if ($user->clinic_id !== $model->clinic_id) {
            return false;
        }

        // Puede editar su propio perfil
        if ($user->id === $model->id) {
            return true;
        }

        // Solo admins pueden editar otros usuarios
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determina si el usuario puede eliminar un usuario
     */
    public function delete(User $user, User $model): bool
    {
        // No puede eliminarse a sí mismo
        if ($user->id === $model->id) {
            return false;
        }

        // Validación de tenant
        if ($user->clinic_id !== $model->clinic_id) {
            return false;
        }

        // Solo admins pueden eliminar usuarios
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determina si el usuario puede restaurar usuarios eliminados (soft delete)
     */
    public function restore(User $user, User $model): bool
    {
        return $user->role === UserRole::ADMIN
            && $user->clinic_id === $model->clinic_id;
    }

    /**
     * Determina si el usuario puede cambiar el rol de otro usuario
     */
    public function changeRole(User $user, User $model): bool
    {
        // Solo admins pueden cambiar roles
        if ($user->role !== UserRole::ADMIN) {
            return false;
        }

        // No puede cambiar su propio rol
        if ($user->id === $model->id) {
            return false;
        }

        // Validación de tenant
        return $user->clinic_id === $model->clinic_id;
    }
}
