<div class="min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <x-title-section title="Gestión de Personal"
            description="Administra los accesos y roles del equipo médico y administrativo.">
            <x-button.button-link href="{{ route('users.create') }}" text="Invitar Usuario"
                icon="<svg class='-ml-1 mr-2 h-5 w-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 6v6m0 0v6m0-6h6m-6 0H6' />
                </svg>" />
        </x-title-section>

        <!-- Dashboard Stats (Mini) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Usuarios</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $users->total() }}</p>
                </div>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="bg-white p-2 rounded-2xl shadow-sm border border-gray-100 mb-8 flex items-center">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="block w-full pl-11 pr-4 py-3 border-none focus:ring-0 rounded-2xl text-gray-700 placeholder-gray-400"
                    placeholder="Buscar por nombre o email...">
            </div>
            @if ($search)
                <button wire:click="$set('search', '')"
                    class="px-4 text-gray-400 hover:text-gray-600 transition-colors text-sm font-medium">
                    Limpiar búsqueda
                </button>
            @endif
        </div>

        <!-- Users Table -->
        <div class="bg-white shadow-xl shadow-gray-200/50 rounded-3xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50/50 text-gray-400">
                            <th scope="col" class="px-8 py-5 text-left text-xs font-bold uppercase tracking-widest">
                                Identidad
                            </th>
                            <th scope="col" class="px-8 py-5 text-left text-xs font-bold uppercase tracking-widest">
                                Rol
                            </th>
                            <th scope="col" class="px-8 py-5 text-left text-xs font-bold uppercase tracking-widest">
                                Estado
                            </th>
                            <th scope="col"
                                class="px-8 py-5 text-center text-xs font-bold uppercase tracking-widest">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($users as $user)
                            <tr class="group hover:bg-blue-50/20 transition-all duration-300"
                                wire:key="user-{{ $user->id }}">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-12 w-12 group-hover:scale-105 transition-transform duration-300">
                                            <div
                                                class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-lg font-bold shadow-lg shadow-blue-200">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900">
                                                {{ $user->name }}
                                                @if ($user->id === auth()->id())
                                                    <span
                                                        class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 uppercase tracking-tighter">Tú</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 font-medium">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    @if ($user->id === auth()->id())
                                        <div
                                            class="px-3 py-1 bg-gray-100 text-gray-600 text-[11px] font-bold rounded border border-gray-200 uppercase w-fit">
                                            {{ $user->role->label() }}
                                        </div>
                                    @else
                                        <select wire:change="changeRole({{ $user->id }}, $event.target.value)"
                                            class="block w-full min-w-[140px] px-3 py-2 text-xs font-bold border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 rounded transition-all duration-300 bg-gray-100 hover:border-black cursor-pointer">
                                            @foreach ($roles as $value => $label)
                                                <option value="{{ $value }}" @selected($user->role->value === $value)>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    @if ($user->id === auth()->id())
                                        <div
                                            class="px-3 py-1 bg-green-50 text-green-600 text-[11px] font-bold rounded-lg border border-green-100 uppercase w-fit">
                                            Activo
                                        </div>
                                    @else
                                        <select wire:change="updateStatus({{ $user->id }}, $event.target.value)"
                                            class="block w-full min-w-[120px] px-3 py-2 text-xs font-bold border-gray-200 focus:outline-none focus:ring-2 {{ $user->is_active ? 'focus:ring-green-100 focus:border-green-400' : 'focus:ring-red-100 focus:border-red-400' }} rounded-xl transition-all duration-300 {{ $user->is_active ? 'bg-green-50/50 text-green-700' : 'bg-red-50/50 text-red-700' }} hover:bg-white cursor-pointer uppercase">
                                            <option value="1" @selected($user->is_active)>Activo</option>
                                            <option value="0" @selected(!$user->is_active)>Inactivo</option>
                                        </select>
                                    @endif
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-center font-medium">
                                    <div class="flex justify-center gap-2">
                                        @if (!$user->email_verified_at && $user->id !== auth()->id())
                                            <button wire:click="resendInvitation({{ $user->id }})"
                                                wire:loading.attr="disabled"
                                                class="group/btn flex items-center gap-2 text-indigo-600 hover:text-white hover:bg-indigo-600 border border-indigo-100 px-3 py-2 rounded-xl transition-all duration-300 text-[11px] font-bold uppercase tracking-tight">
                                                <svg wire:loading.remove
                                                    wire:target="resendInvitation({{ $user->id }})"
                                                    class="h-3 w-3 transition-transform group-hover/btn:translate-x-1"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                </svg>
                                                <svg wire:loading wire:target="resendInvitation({{ $user->id }})"
                                                    class="animate-spin h-3 w-3" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                                <span>Reenviar</span>
                                            </button>
                                        @elseif($user->id !== auth()->id())
                                            <span
                                                class="text-[10px] font-bold text-gray-300 italic uppercase">Logueado</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="p-4 bg-gray-50 rounded-full mb-4">
                                            <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-base font-bold text-gray-900 uppercase">Sin resultados</h3>
                                        <p class="text-xs text-gray-500 mt-1">No se encontraron miembros para esta
                                            búsqueda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($users->hasPages())
                <div class="px-8 py-6 bg-gray-50 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
