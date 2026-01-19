<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Validation\ValidationException;

final class ActivateUserAction
{

    public function execute(User $user, User $performedBy): User
    {
        if ($user->clinic_id !== $performedBy->clinic_id) {
            throw ValidationException::withMessages([
                'user' => 'No tienes permiso para activar este usuario.',
            ]);
        }

        if ($user->is_active) {
            throw ValidationException::withMessages([
                'user' => 'El usuario ya está activo.',
            ]);
        }

        $user->update([
            'is_active' => true,
        ]);

        activity()
            ->performedOn($user)
            ->causedBy($performedBy)
            ->log("Usuario activado: {$user->name}");

        return $user->fresh();
    }
}