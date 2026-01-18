<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Ver lista de pagos
     */
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    /**
     * Ver un pago específico
     */
    public function view(User $user, Payment $payment): bool
    {
        if ($user->clinic_id !== $payment->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    /**
     * Crear pagos
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    /**
     * Actualizar pagos
     */
    public function update(User $user, Payment $payment): bool
    {
        if ($user->clinic_id !== $payment->clinic_id) {
            return false;
        }

        // Solo admins pueden editar pagos
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Eliminar pagos
     */
    public function delete(User $user, Payment $payment): bool
    {
        if ($user->clinic_id !== $payment->clinic_id) {
            return false;
        }

        // Solo admins pueden eliminar pagos
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Cancelar pagos
     */
    public function cancel(User $user, Payment $payment): bool
    {
        if ($user->clinic_id !== $payment->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN;
    }

    /**
     * Reembolsar pagos
     */
    public function refund(User $user, Payment $payment): bool
    {
        if ($user->clinic_id !== $payment->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN;
    }

    /**
     * Ver reportes financieros
     */
    public function viewReports(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }
}