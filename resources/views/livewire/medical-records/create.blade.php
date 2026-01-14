<div class="p-6 max-h-[80vh] overflow-y-auto">
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900">Nuevo Registro Médico</h3>
        <p class="text-sm text-gray-600">Paciente: <span class="font-medium">{{ $patient->full_name }}</span></p>
    </div>

    <form wire:submit="save">
        {{-- Tipo de registro y fecha --}}
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Información General</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo de Registro <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model="record_type"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                        @foreach($recordTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('record_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha de Consulta <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        wire:model.blur="consultation_date"
                        max="{{ now()->format('Y-m-d') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                    @error('consultation_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Signos Vitales --}}
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Signos Vitales</h4>
            
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Peso (kg)</label>
                    <input 
                        type="number" 
                        step="0.1"
                        wire:model.blur="weight"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="70.5"
                    >
                    @error('weight')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Altura (cm)</label>
                    <input 
                        type="number" 
                        step="0.1"
                        wire:model.blur="height"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="175"
                    >
                    @error('height')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Presión</label>
                    <input 
                        type="text" 
                        wire:model.blur="blood_pressure"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="120/80"
                    >
                    @error('blood_pressure')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Temp. (°C)</label>
                    <input 
                        type="number" 
                        step="0.1"
                        wire:model.blur="temperature"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="36.5"
                    >
                    @error('temperature')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">FC (bpm)</label>
                    <input 
                        type="number" 
                        wire:model.blur="heart_rate"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="72"
                    >
                    @error('heart_rate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Información Clínica (Encriptada) --}}
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                Información Clínica (Encriptada)
            </h4>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Motivo de Consulta
                    </label>
                    <input 
                        type="text" 
                        wire:model.blur="chief_complaint"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Ej: Dolor de cabeza persistente"
                    >
                    @error('chief_complaint')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Síntomas <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        wire:model.blur="symptoms"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Describa los síntomas del paciente..."
                    ></textarea>
                    @error('symptoms')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Diagnóstico <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        wire:model.blur="diagnosis"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Diagnóstico médico..."
                    ></textarea>
                    @error('diagnosis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Plan de Tratamiento
                    </label>
                    <textarea 
                        wire:model.blur="treatment_plan"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Tratamiento recomendado..."
                    ></textarea>
                    @error('treatment_plan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Prescripciones / Medicamentos
                    </label>
                    <textarea 
                        wire:model.blur="prescriptions"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Medicamentos recetados con dosis..."
                    ></textarea>
                    @error('prescriptions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Notas Clínicas <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        wire:model.blur="clinical_notes"
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Observaciones adicionales, evolución, etc..."
                    ></textarea>
                    @error('clinical_notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-xs text-green-800 flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <span>
                        Toda la información clínica se almacena encriptada en la base de datos para garantizar la privacidad del paciente.
                    </span>
                </p>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex justify-end gap-3 pt-4 border-t sticky bottom-0 bg-white">
    <a href="{{ route('patients.show', $patient) }}" 
       wire:navigate
       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
        Cancelar
    </a>
    <button 
        type="submit"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 cursor-pointer"
        wire:loading.attr="disabled"
    >
        <span wire:loading.remove>Guardar Registro</span>
        <span wire:loading>Guardando...</span>
    </button>
</div>
    </form>
</div>