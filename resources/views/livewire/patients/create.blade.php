<div class="p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-6">Crear Nuevo Paciente</h3>

    <form wire:submit="save">
        {{-- Información Personal --}}
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Información Personal</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Nombre --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        wire:model.blur="first_name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Juan"
                    >
                    @error('first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Apellido --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Apellido <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        wire:model.blur="last_name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Pérez"
                    >
                    @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tipo de documento --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo de Documento <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model="identification_type"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="CC">Cédula de Ciudadanía</option>
                        <option value="TI">Tarjeta de Identidad</option>
                        <option value="CE">Cédula de Extranjería</option>
                        <option value="PP">Pasaporte</option>
                    </select>
                    @error('identification_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Número de documento --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Número de Documento <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        wire:model.blur="identification_number"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="1234567890"
                    >
                    @error('identification_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Fecha de nacimiento --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha de Nacimiento <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        wire:model.blur="date_of_birth"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    @error('date_of_birth')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Género --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Género
                    </label>
                    <select 
                        wire:model="gender"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Seleccionar</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                        <option value="O">Otro</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tipo de sangre --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo de Sangre
                    </label>
                    <select 
                        wire:model="blood_type"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Seleccionar</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                    @error('blood_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Información de Contacto --}}
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Información de Contacto</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Teléfono <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="tel" 
                        wire:model.blur="phone"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="3001234567"
                    >
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Celular
                    </label>
                    <input 
                        type="tel" 
                        wire:model="mobile_phone"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="3109876543"
                    >
                    @error('mobile_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email
                    </label>
                    <input 
                        type="email" 
                        wire:model.blur="email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="paciente@ejemplo.com"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Dirección
                    </label>
                    <input 
                        type="text" 
                        wire:model="address"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Calle 123 #45-67"
                    >
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Ciudad
                    </label>
                    <input 
                        type="text" 
                        wire:model="city"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Bogotá"
                    >
                    @error('city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Contacto de Emergencia --}}
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Contacto de Emergencia</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre
                    </label>
                    <input 
                        type="text" 
                        wire:model="emergency_contact_name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="María Pérez"
                    >
                    @error('emergency_contact_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Teléfono
                    </label>
                    <input 
                        type="tel" 
                        wire:model="emergency_contact_phone"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="3201234567"
                    >
                    @error('emergency_contact_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Parentesco
                    </label>
                    <input 
                        type="text" 
                        wire:model="emergency_contact_relationship"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Madre, Hermano, etc."
                    >
                    @error('emergency_contact_relationship')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Notas administrativas --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Notas Administrativas
            </label>
            <textarea 
                wire:model="notes"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Notas adicionales (no médicas)..."
            ></textarea>
            @error('notes')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Botones --}}
        <div class="flex justify-end gap-3 pt-4 border-t">
            <button 
                type="button"
                wire:click="$dispatch('closeModal')"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
            >
                Cancelar
            </button>
            <button 
                type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>Crear Paciente</span>
                <span wire:loading>Guardando...</span>
            </button>
        </div>
    </form>
</div>