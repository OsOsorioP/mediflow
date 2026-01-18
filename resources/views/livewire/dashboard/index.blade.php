<div class="w-max">
    {{-- Header con selector de período --}}
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
            
            <div class="flex gap-2">
                <button 
                    wire:click="setPeriod('week')"
                    class="px-4 py-2 rounded {{ $period === 'week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                    Esta Semana
                </button>
                <button 
                    wire:click="setPeriod('month')"
                    class="px-4 py-2 rounded {{ $period === 'month' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                    Este Mes
                </button>
                <button 
                    wire:click="setPeriod('year')"
                    class="px-4 py-2 rounded {{ $period === 'year' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                    Este Año
                </button>
            </div>
        </div>

        {{-- KPIs Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            {{-- Total Pacientes --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Pacientes</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_patients'] }}</p>
                        <p class="text-xs text-green-600 mt-1">+{{ $stats['new_patients_this_period'] }} nuevos</p>
                    </div>
                    <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Total Citas --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Citas (Período)</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_appointments'] }}</p>
                        <p class="text-xs text-gray-600 mt-1">{{ $stats['completed_appointments'] }} completadas</p>
                    </div>
                    <div class="h-12 w-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Próximas Citas --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Próximas Citas</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['pending_appointments'] }}</p>
                        <p class="text-xs text-gray-600 mt-1">Pendientes y confirmadas</p>
                    </div>
                    <div class="h-12 w-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Registros Médicos --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Registros Médicos</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_medical_records'] }}</p>
                        <p class="text-xs text-gray-600 mt-1">En este período</p>
                    </div>
                    <div class="h-12 w-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grids principales --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Citas de Hoy --}}
        <div class="lg:col-span-2 bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Citas de Hoy</h3>
                <p class="text-sm text-gray-600">{{ today()->locale('es')->isoFormat('dddd, D [de] MMMM') }}</p>
            </div>
            <div class="p-6">
                @if($todayAppointments->count() > 0)
                    <div class="space-y-4">
                        @foreach($todayAppointments as $appointment)
                            <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 border border-gray-200">
                                <div class="text-center">
                                    <div class="text-xl font-bold text-gray-900">
                                        {{ $appointment->scheduled_at->format('H:i') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $appointment->duration_minutes }}min
                                    </div>
                                </div>
                                <div class="flex-1 border-l-4 border-{{ $appointment->status->color() }}-500 pl-3">
                                    <a href="{{ route('patients.show', $appointment->patient) }}" 
                                       class="font-semibold text-gray-900 hover:text-blue-600">
                                        {{ $appointment->patient->full_name }}
                                    </a>
                                    <p class="text-sm text-gray-600">
                                        Dr. {{ $appointment->doctor->name }}
                                        @if($appointment->reason)
                                            · {{ Str::limit($appointment->reason, 40) }}
                                        @endif
                                    </p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $appointment->status->color() }}-100 text-{{ $appointment->status->color() }}-800">
                                    {{ $appointment->status->label() }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500">No hay citas programadas para hoy</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Distribución por Estado --}}
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Distribución de Citas</h3>
            </div>
            <div class="p-6">
                @if($appointmentsByStatus->count() > 0)
                    <div class="space-y-3">
                        @foreach($appointmentsByStatus as $status => $count)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-medium text-gray-700">{{ $status }}</span>
                                    <span class="text-gray-900 font-bold">{{ $count }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" 
                                         style="width: {{ ($count / $stats['total_appointments']) * 100 }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500 py-8">Sin datos</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Próximas Citas y Top Pacientes --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Próximas Citas (7 días) --}}
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Próximas Citas (7 días)</h3>
            </div>
            <div class="p-6">
                @if($upcomingAppointments->count() > 0)
                    <div class="space-y-3">
                        @foreach($upcomingAppointments as $appointment)
                            <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $appointment->patient->full_name }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ $appointment->scheduled_at->locale('es')->isoFormat('ddd D MMM, HH:mm') }}
                                    </p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $appointment->status->color() }}-100 text-{{ $appointment->status->color() }}-800">
                                    {{ $appointment->status->label() }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500 py-8">No hay citas próximas</p>
                @endif
            </div>
        </div>

        {{-- Top Pacientes --}}
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Pacientes Más Frecuentes</h3>
            </div>
            <div class="p-6">
                @if($topPatients->count() > 0)
                    <div class="space-y-3">
                        @foreach($topPatients as $patient)
                            <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-medium text-sm">
                                            {{ strtoupper(substr($patient->first_name, 0, 1)) }}{{ strtoupper(substr($patient->last_name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <a href="{{ route('patients.show', $patient) }}" 
                                           class="font-medium text-gray-900 hover:text-blue-600">
                                            {{ $patient->full_name }}
                                        </a>
                                        <p class="text-sm text-gray-600">{{ $patient->phone }}</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-sm font-medium">
                                    {{ $patient->appointments_count }} citas
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500 py-8">Sin datos</p>
                @endif
            </div>
        </div>
    </div>
</div>