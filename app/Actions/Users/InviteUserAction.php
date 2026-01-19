<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Enums\UserRole;
use App\Mail\UserInvitation;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class InviteUserAction
{

    public function execute(
        Clinic $clinic,
        string $name,
        string $email,
        UserRole $role,
        ?string $phone = null
    ): User {

        if (!$clinic->canAddUser()) {
            throw ValidationException::withMessages([
                'clinic' => 'Has alcanzado el límite máximo de usuarios para tu plan. Actualiza tu suscripción.',
            ]);
        }

        $existingUser = User::where('clinic_id', $clinic->id)
            ->where('email', $email)
            ->first();

        if ($existingUser) {
            throw ValidationException::withMessages([
                'email' => 'Ya existe un usuario con este email en tu clínica.',
            ]);
        }

        return DB::transaction(function () use ($clinic, $name, $email, $role, $phone) {
            
            $temporaryPassword = Str::random(16);

            $user = User::create([
                'clinic_id' => $clinic->id,
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($temporaryPassword),
                'phone' => $phone,
                'role' => $role,
                'is_active' => true,
            ]);

            Mail::to($user->email)->send(
                new UserInvitation($user, $temporaryPassword, $clinic)
            );

            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->log("Usuario invitado: {$user->name} ({$user->email})");

            return $user;
        });
    }
}