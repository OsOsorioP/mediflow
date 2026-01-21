<?php

namespace App\Livewire\Users;

use App\Actions\Users\InviteUserAction;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Create extends Component
{
    public $form = [
        'name' => '',
        'email' => '',
        'role' => 'assistant',
        'phone' => '',
    ];

    public function mount()
    {
        Gate::authorize('create', User::class);
    }

    public function save(InviteUserAction $inviteUserAction)
    {
        Gate::authorize('create', User::class);

        $this->validate([
            'form.name' => 'required|string|max:255',
            'form.email' => 'required|email|max:255',
            'form.role' => 'required|string|in:admin,assistant',
            'form.phone' => 'nullable|string|max:20',
        ]);

        /** @var User $authUser */
        $authUser = auth()->user();

        try {
            $inviteUserAction->execute(
                $authUser->clinic,
                $this->form['name'],
                $this->form['email'],
                UserRole::from($this->form['role']),
                $this->form['phone']
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Usuario invitado correctamente. Se ha enviado un correo con las credenciales.'
            ]);

            return redirect()->route('users.index');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al invitar al usuario: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.users.create', [
            'roles' => UserRole::options(),
        ]);
    }
}
