<div class="min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs / Back Link -->
        <div class="mb-8">
            <a href="{{ route('users.index') }}"
                class="inline-flex items-center text-sm font-bold text-gray-500 hover:text-blue-600 transition-colors group">
                <svg class="mr-2 h-4 w-4 transform group-hover:-translate-x-1 transition-transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver a Gestión de Personal
            </a>
        </div>

        <div class="bg-white shadow-2xl shadow-gray-200/50 rounded-3xl border border-gray-100 overflow-hidden">
            <div class="p-8 md:p-12">
                <div class="flex items-center gap-6 mb-10">
                    <div class="p-4 bg-blue-100 rounded-2xl text-blue-600 shadow-sm">
                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 leading-tight">Invitar nuevo miembro</h2>
                        <p class="text-base text-gray-500 font-bold uppercase tracking-tight mt-1">Expande el equipo
                            médico de tu clínica</p>
                    </div>
                </div>

                <form wire:submit="save" class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="col-span-1 md:col-span-2">
                            <x-input-label for="name" value="Nombre Completo"
                                class="text-gray-700 font-black ml-1 mb-2" />
                            <x-text-input id="name" type="text"
                                class="mt-1 block w-full rounded-2xl border-gray-100 focus:border-blue-500 focus:ring-4 focus:ring-blue-50/50 transition-all bg-gray-50 px-6 py-4 text-lg font-medium"
                                wire:model="form.name" required placeholder="Ej. Dr. Mauricio Colmenero" />
                            <x-input-error :messages="$errors->get('form.name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" value="Correo Electrónico"
                                class="text-gray-700 font-black ml-1 mb-2" />
                            <x-text-input id="email" type="email"
                                class="mt-1 block w-full rounded-2xl border-gray-100 focus:border-blue-500 focus:ring-4 focus:ring-blue-50/50 transition-all bg-gray-50 px-6 py-4 text-lg font-medium"
                                wire:model="form.email" required placeholder="mauricio@ejemplo.com" />
                            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="phone" value="Teléfono de Contacto"
                                class="text-gray-700 font-black ml-1 mb-2" />
                            <x-text-input id="phone" type="text"
                                class="mt-1 block w-full rounded-2xl border-gray-100 focus:border-blue-500 focus:ring-4 focus:ring-blue-50/50 transition-all bg-gray-50 px-6 py-4 text-lg font-medium"
                                wire:model="form.phone" placeholder="+34 654 321 000" />
                            <x-input-error :messages="$errors->get('form.phone')" class="mt-2" />
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <x-input-label for="role" value="Asignar Rol"
                                class="text-gray-700 font-black ml-1 mb-3" />
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach ($roles as $value => $label)
                                    <label
                                        class="relative flex flex-col cursor-pointer rounded-3xl border-2 p-6 shadow-sm focus:outline-none transition-all duration-300 {{ $form['role'] === $value ? 'border-blue-500 bg-blue-50/50 ring-4 ring-blue-50/30' : 'border-gray-100 hover:border-blue-200 bg-white' }}">
                                        <input type="radio" wire:model="form.role" value="{{ $value }}"
                                            class="sr-only">

                                        <div class="flex items-center justify-between mb-4">
                                            <span
                                                class="text-xl font-black {{ $form['role'] === $value ? 'text-blue-700' : 'text-gray-900' }}">{{ $label }}</span>
                                            @if ($form['role'] === $value)
                                                <div class="text-blue-600">
                                                    <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <span
                                            class="text-sm font-bold uppercase tracking-wide {{ $form['role'] === $value ? 'text-blue-600' : 'text-gray-400' }}">
                                            {{ $value === 'admin' ? 'Acceso completo al sistema, configuración y gestión de clínica.' : 'Gestión de pacientes, citas y registros médicos básicos.' }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('form.role')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-12 flex items-center justify-end gap-6 border-t border-gray-100 pt-10">
                        <a href="{{ route('users.index') }}"
                            class="px-10 py-4 rounded-2xl text-base font-black text-gray-500 hover:bg-gray-100 transition-all uppercase tracking-widest text-center">
                            Cancelar
                        </a>

                        <button type="submit"
                            class="inline-flex items-center px-12 py-4 bg-blue-600 text-white rounded-2xl text-base font-black shadow-2xl shadow-blue-200 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100 transition-all transform hover:-translate-y-1 uppercase tracking-widest min-w-[200px] justify-center text-center">
                            <span wire:loading.remove wire:target="save">Invitar al equipo</span>
                            <div wire:loading wire:target="save" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Procesando...
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
