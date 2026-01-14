<?php

use App\Models\User;
use App\Models\Clinic;
use App\Enums\UserRole;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    
    // Nuevos campos para la clínica
    public string $clinic_name = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'clinic_name' => ['required', 'string', 'max:255'], // Validación de clínica
        ]);

        // Usamos una transacción para que no se cree la clínica si el usuario falla o viceversa
        $user = DB::transaction(function () use ($validated) {
            // 1. Crear la clínica
            $clinic = Clinic::create([
                'name' => $validated['clinic_name'],
                'slug' => Str::slug($validated['clinic_name']) . '-' . Str::random(5),
                'is_active' => true,
            ]);

            // 2. Crear el usuario administrador vinculado a esa clínica
            return User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'clinic_id' => $clinic->id, // Aquí se soluciona el error de la DB
                'role' => UserRole::ADMIN,   // El que se registra es el dueño/médico
                'is_active' => true,
            ]);
        });

        event(new Registered($user));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <form wire:submit="register">
        <!-- Clinic Name (Añadir esto al principio del form) -->
        <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-100">
            <x-input-label for="clinic_name" :value="__('Nombre de tu Clínica o Consultorio')" />
            <x-text-input wire:model="clinic_name" id="clinic_name" class="block mt-1 w-full" type="text" name="clinic_name" required autofocus placeholder="Ej: Centro Médico Salud" />
            <x-input-error :messages="$errors->get('clinic_name')" class="mt-2" />
        </div>

        <!-- Name -->
        <div class="mt-4">
            <x-input-label for="name" :value="__('Tu Nombre Completo')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div>
