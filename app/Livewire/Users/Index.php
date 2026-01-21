<?php

namespace App\Livewire\Users;

use App\Actions\Users\DeactivateUserAction;
use App\Actions\Users\ActivateUserAction;
use App\Actions\Users\ChangeRoleAction;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search' => ['except' => '']];

    public function resendInvitation(User $user)
    {
        Gate::authorize('create', User::class);

        if ($user->id === auth()->id()) {
            return;
        }

        try {
            $temporaryPassword = \Illuminate\Support\Str::random(16);

            $user->update([
                'password' => \Illuminate\Support\Facades\Hash::make($temporaryPassword),
            ]);

            /** @var User $authUser */
            $authUser = auth()->user();

            \Illuminate\Support\Facades\Mail::to($user->email)->send(
                new \App\Mail\UserInvitation($user, $temporaryPassword, $authUser->clinic)
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Invitación reenviada correctamente.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al reenviar la invitación: ' . $e->getMessage()
            ]);
        }
    }

    public function updateStatus(User $user, $status, DeactivateUserAction $deactivateAction, ActivateUserAction $activateAction)
    {
        Gate::authorize('update', $user);

        /** @var User $authUser */
        $authUser = auth()->user();

        if ($user->id === $authUser->id) {
            return;
        }

        try {
            // Convertimos a boolean si es string desde el select
            $isActive = (bool) $status;

            if (!$isActive && $user->is_active) {
                $deactivateAction->execute($user, $authUser);
                $message = 'Usuario desactivado correctamente.';
            } elseif ($isActive && !$user->is_active) {
                $activateAction->execute($user, $authUser);
                $message = 'Usuario activado correctamente.';
            } else {
                return; // No hay cambio
            }

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $message
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ]);
        }
    }

    public function changeRole(User $user, $newRole, ChangeRoleAction $changeRoleAction)
    {
        Gate::authorize('update', $user);

        /** @var User $authUser */
        $authUser = auth()->user();

        try {
            $changeRoleAction->execute($user, UserRole::from($newRole), $authUser);
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Rol actualizado correctamente.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al actualizar el rol: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        /** @var User $authUser */
        $authUser = auth()->user();

        $users = User::query()
            ->where('clinic_id', $authUser->clinic_id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.users.index', [
            'users' => $users,
            'roles' => UserRole::options(),
        ]);
    }
}
