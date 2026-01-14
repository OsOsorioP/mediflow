<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Información de la Clínica -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Información de la Clínica</h3>
            
            <!-- Clinic Name -->
            <div class="mb-4">
                <x-input-label for="clinic_name" :value="__('Nombre del Consultorio/Clínica')" />
                <x-text-input id="clinic_name" class="block mt-1 w-full" type="text" name="clinic_name" 
                    :value="old('clinic_name')" required autofocus placeholder="Ej: Consultorio Dr. Pérez" />
                <x-input-error :messages="$errors->get('clinic_name')" class="mt-2" />
            </div>

            <!-- Clinic Phone -->
            <div>
                <x-input-label for="clinic_phone" :value="__('Teléfono de la Clínica (opcional)')" />
                <x-text-input id="clinic_phone" class="block mt-1 w-full" type="text" name="clinic_phone" 
                    :value="old('clinic_phone')" placeholder="Ej: 300 123 4567" />
                <x-input-error :messages="$errors->get('clinic_phone')" class="mt-2" />
            </div>
        </div>

        <!-- Información del Usuario Administrador -->
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Tu Cuenta (Administrador)</h3>
            
            <!-- Name -->
            <div class="mb-4">
                <x-input-label for="name" :value="__('Nombre Completo')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" 
                    :value="old('name')" required placeholder="Ej: Dr. Juan Pérez" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mb-4">
                <x-input-label for="email" :value="__('Correo Electrónico')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" 
                    :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <x-input-label for="password" :value="__('Contraseña')" />
                <x-text-input id="password" class="block mt-1 w-full"
                    type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                    type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('¿Ya tienes cuenta?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Registrar Clínica') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>