<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 w-full h-full items-center justify-center">
    <!-- Primary Navigation Menu -->
    <div class="flex flex-col py-4 justify-center">
        <div class="flex flex-col h-full items-center justify-center">
            <div class="flex flex-col items-center">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <x-application-logo class="block h-9 w-9 text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:flex-col w-full gap-8 mt-8">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('patients.index')" :active="request()->routeIs('patients.index')" wire:navigate>
                        {{ __('Patients') }}
                    </x-nav-link>
                    <x-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.index')" wire:navigate>
                        {{ __('Appointments') }}
                    </x-nav-link>
                    <x-nav-link :href="route('payments.index')" :active="request()->routeIs('payments.index')" wire:navigate>
                        {{ __('Payments') }}
                    </x-nav-link>
                </div>
            </div>
            
            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('patients.index')" :active="request()->routeIs('patients.index')" wire:navigate>
                {{ __('Patients') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.index')" wire:navigate>
                {{ __('Appointments') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('payments.index')" :active="request()->routeIs('payments.index')" wire:navigate>
                {{ __('Payments') }}
            </x-responsive-nav-link>
        </div>
    </div>
</nav>
