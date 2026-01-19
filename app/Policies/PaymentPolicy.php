<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->clinic_id !== $payment->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::ADMIN || $user->role === UserRole::ASSISTANT;
    }

    public function update(User $user, Payment $payment): bool
    {
        if ($user->clinic_id !== $payment->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN;
    }

    public function delete(User $user, Payment $payment): bool
    {
        if ($user->clinic_id !== $payment->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN;
    }

    public function cancel(User $user, Payment $payment): bool
    {
        if ($user->clinic_id !== $payment->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN;
    }

    public function refund(User $user, Payment $payment): bool
    {
        if ($user->clinic_id !== $payment->clinic_id) {
            return false;
        }

        return $user->role === UserRole::ADMIN;
    }

    public function viewReports(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }
}