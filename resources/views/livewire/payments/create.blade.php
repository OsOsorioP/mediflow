<div class="p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-6">Registrar Pago</h3>

    <form wire:submit="save">
        {{-- Paciente y Cita --}}
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Información del Paciente</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Paciente <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model.live="patient_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccionar paciente</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">
                                {{ $patient->full_name }} - {{ $patient->identification_number }}
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Cita Asociada (opcional)
                    </label>
                    <select 
                        wire:model="appointment_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        {{ !$patient_id ? 'disabled' : '' }}>
                        <option value="">Sin cita asociada</option>
                        @foreach($appointments as $apt)
                            <option value="{{ $apt['id'] }}">{{ $apt['label'] }}</option>
                        @endforeach
                    </select>
                    @error('appointment_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Información del Pago --}}
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Detalles del Pago</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Monto <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        step="0.01"
                        wire:model.blur="amount"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Moneda <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model="currency"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @foreach($currencies as $code => $name)
                            <option value="{{ $code }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('currency')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha de Pago <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        wire:model.blur="payment_date"
                        max="{{ today()->format('Y-m-d') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('payment_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Método de Pago <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model="payment_method"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @foreach($paymentMethods as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('payment_method')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Estado <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @foreach($paymentStatuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Concepto y Descripción --}}
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Detalles Adicionales</h4>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Concepto <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        wire:model.blur="concept"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Ej: Consulta General, Procedimiento, Control">
                    @error('concept')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Descripción (opcional)
                    </label>
                    <textarea 
                        wire:model.blur="description"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Detalles adicionales del pago..."></textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Número de Referencia (opcional)
                    </label>
                    <input 
                        type="text" 
                        wire:model.blur="reference_number"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Número de transacción, cheque, etc.">
                    <p class="mt-1 text-xs text-gray-500">
                        Para transferencias, tarjetas o cheques
                    </p>
                    @error('reference_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex justify-end gap-3 pt-4 border-t">
            <button 
                type="button"
                wire:click="$dispatch('closeModal')"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancelar
            </button>
            <button 
                type="submit"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                wire:loading.attr="disabled">
                <span wire:loading.remove>Registrar Pago</span>
                <span wire:loading>Guardando...</span>
            </button>
        </div>
    </form>
</div>