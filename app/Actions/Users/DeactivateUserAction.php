<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Validation\ValidationException;

final class DeactivateUserAction
{

    public function execute(User $user, User $performedBy): User
    {

        if ($user->id === $performedBy->id) {
            throw ValidationException::withMessages([
                'user' => 'No puedes desactivar tu propia cuenta.',
            ]);
        }

        if ($user->clinic_id !== $performedBy->clinic_id) {
            throw ValidationException::withMessages([
                'user' => 'No tienes permiso para desactivar este usuario.',
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'user' => 'El usuario ya está desactivado.',
            ]);
        }

        $user->update([
            'is_active' => false,
        ]);

        activity()
            ->performedOn($user)
            ->causedBy($performedBy)
            ->log("Usuario desactivado: {$user->name}");

        return $user->fresh();
    }
}