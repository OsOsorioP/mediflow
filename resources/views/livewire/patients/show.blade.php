<div>
    {{-- Header con información del paciente --}}
    <div class="mb-6 bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-4">
                {{-- Avatar --}}
                <div class="h-20 w-20 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-blue-600 font-bold text-2xl">
                        {{ strtoupper(substr($patient->first_name, 0, 1)) }}{{ strtoupper(substr($patient->last_name, 0, 1)) }}
                    </span>
                </div>
                
                {{-- Información básica --}}
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ $patient->full_name }}
                    </h2>
                    <p class="text-gray-600">
                        {{ $patient->full_identification }} · {{ $patient->age }} años
                    </p>
                    <div class="flex gap-2 mt-2">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $patient->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $patient->is_active ? 'Activo' : 'Archivado' }}
                        </span>
                        @if($patient->blood_type)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Tipo {{ $patient->blood_type }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="flex gap-2">
                @can('update', $patient)
                    <a href="{{ route('patients.edit', $patient) }}"
                        wire:navigate
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 cursor-pointer">
                        Editar Paciente
                    </a>
                @endcan

                <a href="{{ route('patients.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Volver
                </a>
            </div>
        </div>

        {{-- Estadísticas rápidas --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-4 border-t">
            <div class="text-center">
                <p class="text-sm text-gray-600">Total Consultas</p>
                <p class="text-2xl font-bold text-gray-900">{{ $patient->total_consultations }}</p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-600">Última Consulta</p>
                <p class="text-sm font-semibold text-gray-900">
                    {{ $patient->getLatestMedicalRecord()?->consultation_date?->format('d/m/Y') ?? 'N/A' }}
                </p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-600">Teléfono</p>
                <p class="text-sm font-semibold text-gray-900">{{ $patient->phone }}</p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-600">Email</p>
                <p class="text-sm font-semibold text-gray-900">{{ $patient->email ?: 'N/A' }}</p>
            </div>
        </div>
    </div>

    {{-- Tabs de navegación --}}
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex gap-6">
                <button 
                    wire:click="setTab('info')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'info' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Información Personal
                </button>
                <button 
                    wire:click="setTab('medical_records')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'medical_records' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Expediente Médico ({{ $patient->total_consultations }})
                </button>
                @can('viewAny', App\Models\AuditLog::class)
                    <button 
                        wire:click="setTab('audit')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'audit' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Historial de Auditoría
                    </button>
                @endcan
            </nav>
        </div>
    </div>

    {{-- Contenido de las pestañas --}}
    @if($activeTab === 'info')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Información Personal --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Datos Personales</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nombre Completo</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $patient->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Documento</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $patient->full_identification }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Fecha de Nacimiento</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $patient->date_of_birth->format('d/m/Y') }} ({{ $patient->age }} años)
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Género</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $patient->gender === 'M' ? 'Masculino' : ($patient->gender === 'F' ? 'Femenino' : 'Otro') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tipo de Sangre</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $patient->blood_type ?: 'No especificado' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Información de Contacto --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Contacto</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Teléfono Principal</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $patient->phone }}</dd>
                    </div>
                    @if($patient->mobile_phone)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Celular</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $patient->mobile_phone }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $patient->email ?: 'No especificado' }}</dd>
                    </div>
                    @if($patient->address)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dirección</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $patient->address }}</dd>
                        </div>
                    @endif
                    @if($patient->city)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ciudad</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $patient->city }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Contacto de Emergencia --}}
            @if($patient->emergency_contact_name)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contacto de Emergencia</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nombre</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $patient->emergency_contact_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $patient->emergency_contact_phone }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Parentesco</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $patient->emergency_contact_relationship }}</dd>
                        </div>
                    </dl>
                </div>
            @endif

            {{-- Notas Administrativas --}}
            @if($patient->notes)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Notas Administrativas</h3>
                    <p class="text-sm text-gray-700">{{ $patient->notes }}</p>
                </div>
            @endif
        </div>
    @endif

    @if($activeTab === 'medical_records')
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Expediente Médico</h3>
                @can('create', App\Models\MedicalRecord::class)
                    <a href="{{ route('medical-records.create', $patient) }}" 
                        wire:navigate
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        + Nuevo Registro
                    </a>
                @endcan
            </div>

            @if($medicalRecords->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($medicalRecords as $record)
                        <div class="p-6 hover:bg-gray-50">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $record->record_type->color() }}-100 text-{{ $record->record_type->color() }}-800">
                                        {{ $record->record_type->label() }}
                                    </span>
                                    <p class="mt-2 text-sm text-gray-600">
                                        {{ $record->consultation_date->format('d/m/Y') }} · 
                                        Dr. {{ $record->creator->name }}
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    @can('view', $record)
                                        <button 
                                            wire:click="$dispatch('openModal', { component: 'medical-records.view', arguments: { recordId: {{ $record->id }} } })"
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                            Ver Detalles
                                        </button>
                                    @endcan
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                @if($record->chief_complaint)
                                    <div>
                                        <p class="text-gray-500">Motivo</p>
                                        <p class="font-medium text-gray-900">{{ $record->chief_complaint }}</p>
                                    </div>
                                @endif
                                @if($record->weight)
                                    <div>
                                        <p class="text-gray-500">Peso</p>
                                        <p class="font-medium text-gray-900">{{ $record->weight }} kg</p>
                                    </div>
                                @endif
                                @if($record->blood_pressure)
                                    <div>
                                        <p class="text-gray-500">Presión Arterial</p>
                                        <p class="font-medium text-gray-900">{{ $record->blood_pressure }}</p>
                                    </div>
                                @endif
                                @if($record->bmi)
                                    <div>
                                        <p class="text-gray-500">IMC</p>
                                        <p class="font-medium text-gray-900">{{ $record->bmi }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="p-6 border-t">
                    {{ $medicalRecords->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <p class="text-gray-500">No hay registros médicos para este paciente</p>
                </div>
            @endif
        </div>
    @endif

    @if($activeTab === 'audit' && $auditLogs)
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Historial de Auditoría</h3>
                <p class="text-sm text-gray-600 mt-1">Registro completo de accesos y modificaciones</p>
            </div>

            @if($auditLogs->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($auditLogs as $log)
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-1">
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full 
                                        {{ $log->action === 'created' ? 'bg-green-100 text-green-600' : '' }}
                                        {{ $log->action === 'updated' ? 'bg-blue-100 text-blue-600' : '' }}
                                        {{ $log->action === 'viewed' ? 'bg-gray-100 text-gray-600' : '' }}
                                        {{ $log->action === 'deleted' ? 'bg-red-100 text-red-600' : '' }}">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900">{{ $log->action_description }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $log->created_at->diffForHumans() }} · 
                                        IP: {{ $log->ip_address }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="p-6 border-t">
                    {{ $auditLogs->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <p class="text-gray-500">No hay registros de auditoría</p>
                </div>
            @endif
        </div>
    @endif
</div>