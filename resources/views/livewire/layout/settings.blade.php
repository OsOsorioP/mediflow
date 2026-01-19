<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{
    openNotifications: false,
    openMessages: false,
    openProfile: false,
    notifications: [
        { id: 1, title: 'Nueva cita agendada', message: 'Carlos Ramírez - Hoy 3:00 PM', time: 'Hace 5 min', unread: true },
        { id: 2, title: 'Pago recibido', message: '$50.000 - Consulta general', time: 'Hace 1 hora', unread: true },
        { id: 3, title: 'Registro médico completado', message: 'Paciente: María López', time: 'Hace 2 horas', unread: false }
    ],
    messages: [
        { id: 1, from: 'Dr. González', message: 'Revisé el historial del paciente...', time: 'Hace 10 min', unread: true },
        { id: 2, from: 'Recepción', message: 'Paciente esperando en sala', time: 'Hace 30 min', unread: true }
    ],
    unreadNotifications() {
        return this.notifications.filter(n => n.unread).length;
    },
    unreadMessages() {
        return this.messages.filter(m => m.unread).length;
    }
}" class="bg-white border-b border-gray-200 max-w-full sticky top-0 z-50 shadow-sm">

    <!-- Primary Navigation Menu -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- Left side - Search Bar -->
            <div class="flex items-center flex-1 max-w-2xl">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" placeholder="Buscar pacientes, citas..."
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-gray-50 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <kbd
                            class="hidden sm:inline-block px-2 py-1 text-xs font-semibold text-gray-500 bg-white border border-gray-200 rounded">⌘K</kbd>
                    </div>
                </div>
            </div>

            <!-- Right side - Actions -->
            <div class="flex items-center gap-2 ml-4">

                <!-- Notifications Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open"
                        class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-150">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span x-show="unreadNotifications() > 0"
                            class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full"
                            x-text="unreadNotifications()"></span>
                    </button>

                    <!-- Notifications Dropdown Panel -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
                        style="display: none;">

                        <div class="px-4 py-2 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900">Notificaciones</h3>
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            <template x-for="notification in notifications" :key="notification.id">
                                <div
                                    class="px-4 py-3 hover:bg-gray-50 transition-colors cursor-pointer border-b border-gray-100 last:border-0">
                                    <div class="flex items-start">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900" x-text="notification.title">
                                            </p>
                                            <p class="text-sm text-gray-600 mt-1" x-text="notification.message"></p>
                                            <p class="text-xs text-gray-500 mt-1" x-text="notification.time"></p>
                                        </div>
                                        <div x-show="notification.unread" class="ml-2 flex-shrink-0">
                                            <span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="px-4 py-2 border-t border-gray-200">
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Ver todas
                                las notificaciones</a>
                        </div>
                    </div>
                </div>

                <!-- Messages Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open"
                        class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-150">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span x-show="unreadMessages() > 0"
                            class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full"
                            x-text="unreadMessages()"></span>
                    </button>

                    <!-- Messages Dropdown Panel -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
                        style="display: none;">

                        <div class="px-4 py-2 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900">Mensajes</h3>
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            <template x-for="message in messages" :key="message.id">
                                <div
                                    class="px-4 py-3 hover:bg-gray-50 transition-colors cursor-pointer border-b border-gray-100 last:border-0">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-blue-600"
                                                    x-text="message.from.charAt(0)"></span>
                                            </div>
                                        </div>
                                        <div class="ml-3 flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900" x-text="message.from"></p>
                                            <p class="text-sm text-gray-600 mt-1 truncate" x-text="message.message">
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1" x-text="message.time"></p>
                                        </div>
                                        <div x-show="message.unread" class="ml-2 flex-shrink-0">
                                            <span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="px-4 py-2 border-t border-gray-200">
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Ver todos
                                los mensajes</a>
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown -->
                <div class="relative ml-2" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open"
                        class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-150">
                        <div
                            class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden md:block text-left">
                            <div class="text-sm font-medium text-gray-900" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                                x-on:profile-updated.window="name = $event.detail.name"></div>
                            <div class="text-xs text-gray-500">{{ auth()->user()->role->label() }}</div>
                        </div>
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Profile Dropdown Panel -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
                        style="display: none;">

                        <div class="px-4 py-3 border-b border-gray-200">
                            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ auth()->user()->email }}</p>
                        </div>

                        <a href="{{ route('profile') }}" wire:navigate
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Mi Perfil
                            </div>
                        </a>

                        <button wire:click="logout"
                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors border-t border-gray-200 mt-2">
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Cerrar Sesión
                            </div>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</nav>
