<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Validation\ValidationException;

final class ChangeRoleAction
{

    public function execute(
        User $user,
        UserRole $newRole,
        User $performedBy
    ): User {

        if ($user->id === $performedBy->id) {
            throw ValidationException::withMessages([
                'user' => 'No puedes cambiar tu propio rol.',
            ]);
        }

        if ($user->clinic_id !== $performedBy->clinic_id) {
            throw ValidationException::withMessages([
                'user' => 'No tienes permiso para modificar este usuario.',
            ]);
        }

        if ($user->role === $newRole) {
            throw ValidationException::withMessages([
                'role' => "El usuario ya tiene el rol {$newRole->label()}.",
            ]);
        }

        $oldRole = $user->role;

        $user->update([
            'role' => $newRole,
        ]);

        activity()
            ->performedOn($user)
            ->causedBy($performedBy)
            ->withProperties([
                'old_role' => $oldRole->value,
                'new_role' => $newRole->value,
            ])
            ->log("Rol cambiado de {$oldRole->label()} a {$newRole->label()}");

        return $user->fresh();
    }
}