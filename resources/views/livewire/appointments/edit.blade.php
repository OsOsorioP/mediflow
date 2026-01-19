<div class="p-6 z-20">
    <h3 class="text-lg font-semibold text-gray-900 mb-6">Editar Cita</h3>

    <form wire:submit="save">
        {{-- Selección de Paciente y Médico --}}
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Información Básica</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Paciente --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Paciente <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.blur="patient_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccionar paciente</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}">
                                {{ $patient->full_name }} - {{ $patient->identification_number }}
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Médico --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Médico/Profesional <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.live="user_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccionar médico</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}">
                                Dr. {{ $doctor->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Fecha y Duración --}}
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Fecha y Hora</h4>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Fecha --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha <span class="text-red-500">*</span>
                    </label>
                    <input type="date" wire:model.live="appointment_date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('appointment_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Duración --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Duración (minutos) <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.live="duration_minutes"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="15">15 minutos</option>
                        <option value="20">20 minutos</option>
                        <option value="30">30 minutos</option>
                        <option value="45">45 minutos</option>
                        <option value="60">60 minutos</option>
                        <option value="90">90 minutos</option>
                    </select>
                    @error('duration_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tipo de cita --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo de Cita
                    </label>
                    <select wire:model="appointment_type"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @foreach ($appointmentTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Slots disponibles --}}
        @if (count($availableSlots) > 0)
            <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">
                    Horarios Disponibles
                    <span class="text-xs font-normal text-gray-500">({{ count($availableSlots) }} slots)</span>
                </h4>

                <div class="grid grid-cols-4 md:grid-cols-6 gap-2 max-h-64 overflow-y-auto p-2 bg-gray-50 rounded">
                    @foreach ($availableSlots as $slot)
                        <button type="button" wire:click="selectSlot('{{ $slot }}')"
                            class="px-3 py-2 text-sm rounded border {{ $appointment_time === $slot ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-blue-50' }}">
                            {{ $slot }}
                        </button>
                    @endforeach
                </div>

                @if ($appointment_time)
                    <p class="mt-2 text-sm text-green-700">
                        ✓ Horario seleccionado: <strong>{{ $appointment_time }}</strong>
                    </p>
                @endif

                @error('appointment_time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @elseif($user_id && $appointment_date)
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded">
                <p class="text-sm text-yellow-800">
                    No hay horarios disponibles para la fecha seleccionada.
                </p>
            </div>
        @else
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded">
                <p class="text-sm text-blue-800">
                    Seleccione un médico y una fecha para ver los horarios disponibles.
                </p>
            </div>
        @endif

        {{-- Información adicional --}}
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Información Adicional</h4>

            <div class="space-y-4">
                {{-- Motivo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Motivo de la Consulta
                    </label>
                    <input type="text" wire:model.blur="reason"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Ej: Control general, dolor de cabeza, etc.">
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notas --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Notas Administrativas
                    </label>
                    <textarea wire:model.blur="notes" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Notas internas, recordatorios, etc."></textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" wire:click="$dispatch('closeModal')"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancelar
            </button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                wire:loading.attr="disabled" :disabled="!$wire.appointment_time">
                <span wire:loading.remove>Guardar Cambios</span>
                <span wire:loading>Guardando...</span>
            </button>
        </div>
    </form>
</div>
