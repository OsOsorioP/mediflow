<div>
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Agenda de Citas</h2>

            @can('create', App\Models\Appointment::class)
                <a href="{{ route('appointments.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                    + Nueva Cita
                </a>
            @endcan
        </div>

        {{-- Estadísticas del día --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Total</p>
                <p class="text-2xl font-bold text-gray-900">{{ $todayStats['total'] }}</p>
            </div>
            <div class="bg-yellow-50 rounded-lg shadow p-4">
                <p class="text-sm text-yellow-800">Pendientes</p>
                <p class="text-2xl font-bold text-yellow-900">{{ $todayStats['pending'] }}</p>
            </div>
            <div class="bg-blue-50 rounded-lg shadow p-4">
                <p class="text-sm text-blue-800">Confirmadas</p>
                <p class="text-2xl font-bold text-blue-900">{{ $todayStats['confirmed'] }}</p>
            </div>
            <div class="bg-green-50 rounded-lg shadow p-4">
                <p class="text-sm text-green-800">Completadas</p>
                <p class="text-2xl font-bold text-green-900">{{ $todayStats['completed'] }}</p>
            </div>
            <div class="bg-red-50 rounded-lg shadow p-4">
                <p class="text-sm text-red-800">Canceladas</p>
                <p class="text-2xl font-bold text-red-900">{{ $todayStats['cancelled'] }}</p>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                {{-- Fecha --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                    <input type="date" wire:model.live="filterDate"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Estado --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select wire:model.live="filterStatus"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Médico --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Médico</label>
                    <select wire:model.live="filterDoctor"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Búsqueda --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar Paciente</label>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nombre del paciente..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>
    </div>

    {{-- Mensajes flash --}}
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('message') }}
        </div>
    @endif

    {{-- Lista de citas --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if ($appointments->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach ($appointments as $appointment)
                    <div class="p-4 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            {{-- Información principal --}}
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    {{-- Hora --}}
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-gray-900">
                                            {{ $appointment->scheduled_at->format('H:i') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $appointment->duration_minutes }}min
                                        </div>
                                    </div>

                                    <div class="border-l-4 border-{{ $appointment->status->color() }}-500 pl-3 flex-1">
                                        {{-- Paciente --}}
                                        <a href="{{ route('patients.show', $appointment->patient) }}"
                                            class="text-lg font-semibold text-gray-900 hover:text-blue-600">
                                            {{ $appointment->patient->full_name }}
                                        </a>

                                        {{-- Detalles --}}
                                        <div class="flex flex-wrap gap-3 mt-1 text-sm text-gray-600">
                                            <span>
                                                👨‍⚕️ Dr. {{ $appointment->doctor->name }}
                                            </span>
                                            @if ($appointment->appointment_type)
                                                <span>
                                                    📋 {{ $appointment->appointment_type->label() }}
                                                </span>
                                            @endif
                                            @if ($appointment->reason)
                                                <span>
                                                    💬 {{ Str::limit($appointment->reason, 50) }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Estado --}}
                                        <div class="mt-2">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $appointment->status->color() }}-100 text-{{ $appointment->status->color() }}-800">
                                                {{ $appointment->status->label() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Acciones --}}
                            <div class="flex gap-2 ml-4">
                                @if ($appointment->status === App\Enums\AppointmentStatus::PENDING)
                                    @can('confirm', $appointment)
                                        <button wire:click="confirmAppointment({{ $appointment->id }})"
                                            class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                                            Confirmar
                                        </button>
                                    @endcan
                                @endif

                                @if ($appointment->status->isActive() && $appointment->isPast())
                                    @can('complete', $appointment)
                                        <button wire:click="completeAppointment({{ $appointment->id }})"
                                            class="px-3 py-1 text-sm bg-green-100 text-green-700 rounded hover:bg-green-200">
                                            Completar
                                        </button>
                                        <button wire:click="markAsNoShow({{ $appointment->id }})"
                                            wire:confirm="¿Marcar como no asistió?"
                                            class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                            No Asistió
                                        </button>
                                    @endcan
                                @endif

                                @can('cancel', $appointment)
                                    @if ($appointment->canBeCancelled())
                                        <button wire:click="cancelAppointment({{ $appointment->id }})"
                                            wire:confirm="¿Cancelar esta cita?"
                                            class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200">
                                            Cancelar
                                        </button>
                                    @endif
                                @endcan

                                @can('update', $appointment)
                                    @if ($appointment->canBeModified())
                                        <a href="{{ route('appointments.edit', $appointment) }}"
                                            class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                            Editar
                                        </a>
                                    @endif
                                @endcan
                            </div>
                        </div>

                        {{-- Notas si existen --}}
                        @if ($appointment->notes)
                            <div class="mt-3 p-2 bg-yellow-50 rounded text-sm text-gray-700">
                                <strong>Nota:</strong> {{ $appointment->notes }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Paginación --}}
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $appointments->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <p class="text-gray-500">No hay citas para los filtros seleccionados</p>
            </div>
        @endif
    </div>
</div>
