<div>
    {{-- Header con búsqueda y botón crear --}}
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Pacientes</h2>
            
            @can('create', App\Models\Patient::class)
                <button 
                    wire:click="$dispatch('openModal', { component: 'patients.create' })"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium"
                >
                    + Nuevo Paciente
                </button>
            @endcan
        </div>

        {{-- Barra de búsqueda y filtros --}}
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Búsqueda --}}
                <div class="md:col-span-2">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Buscar por nombre, documento, email o teléfono..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                {{-- Filtro por género --}}
                <div>
                    <select 
                        wire:model.live="filterGender"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Todos los géneros</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                        <option value="O">Otro</option>
                    </select>
                </div>

                {{-- Filtro por estado --}}
                <div>
                    <select 
                        wire:model.live="filterActive"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="1">Activos</option>
                        <option value="0">Archivados</option>
                        <option value="">Todos</option>
                    </select>
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

    {{-- Tabla de pacientes --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('first_name')">
                        Nombre
                        @if($sortField === 'first_name')
                            <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Documento
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('date_of_birth')">
                        Edad
                        @if($sortField === 'date_of_birth')
                            <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Contacto
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($patients as $patient)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-medium">
                                        {{ strtoupper(substr($patient->first_name, 0, 1)) }}{{ strtoupper(substr($patient->last_name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $patient->full_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $patient->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $patient->full_identification }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $patient->age }} años
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $patient->phone }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $patient->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $patient->is_active ? 'Activo' : 'Archivado' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                @can('view', $patient)
                                    <a href="{{ route('patients.show', $patient) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        Ver
                                    </a>
                                @endcan

                                @can('update', $patient)
                                    <button 
                                        wire:click="$dispatch('openModal', { component: 'patients.edit', arguments: { patientId: {{ $patient->id }} } })"
                                        class="text-indigo-600 hover:text-indigo-900">
                                        Editar
                                    </button>
                                @endcan

                                @can('archive', $patient)
                                    <button 
                                        wire:click="toggleActive({{ $patient->id }})"
                                        wire:confirm="¿Estás seguro?"
                                        class="text-yellow-600 hover:text-yellow-900">
                                        {{ $patient->is_active ? 'Archivar' : 'Activar' }}
                                    </button>
                                @endcan

                                @can('delete', $patient)
                                    <button 
                                        wire:click="delete({{ $patient->id }})"
                                        wire:confirm="¿Estás seguro de eliminar este paciente?"
                                        class="text-red-600 hover:text-red-900">
                                        Eliminar
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No se encontraron pacientes
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Paginación --}}
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $patients->links() }}
        </div>
    </div>
</div>