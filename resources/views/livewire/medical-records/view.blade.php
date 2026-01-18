<div class="p-6">
    {{-- Header --}}
    <div class="mb-6 pb-4 border-b border-gray-200">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Registro Médico</h3>
                <p class="text-sm text-gray-600 mt-1">
                    Paciente: <strong>{{ $record->patient->full_name }}</strong>
                </p>
                <p class="text-sm text-gray-600">
                    Fecha: <strong>{{ $record->consultation_date->locale('es')->isoFormat('LL') }}</strong>
                </p>
            </div>

            <div class="flex gap-2">
                {{-- Botón Enviar por Email --}}
                <button wire:click="toggleEmailForm"
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Enviar por Email
                </button>
                {{-- Botón Descargar PDF --}}
                <a href="{{ route('medical-records.prescription.download', $record) }}" target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Descargar PDF
                </a>

                {{-- Botón Ver en Nueva Pestaña --}}
                <a href="{{ route('medical-records.prescription.stream', $record) }}" target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Ver PDF
                </a>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-4">
            <span
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $record->record_type->color() }}-100 text-{{ $record->record_type->color() }}-800">
                {{ $record->record_type->label() }}
            </span>
            <span class="text-sm text-gray-600">
                👨‍⚕️ Dr. {{ $record->creator->name }}
            </span>
        </div>
    </div>

    {{-- Formulario de envío por email --}}
    @if($showEmailForm)
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <form wire:submit="sendPdfByEmail">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Enviar PDF a:
                </label>
                <div class="flex gap-2">
                    <input 
                        type="email" 
                        wire:model="emailRecipient"
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="correo@ejemplo.com">
                    <button 
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>Enviar</span>
                        <span wire:loading>Enviando...</span>
                    </button>
                    <button 
                        type="button"
                        wire:click="toggleEmailForm"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Cancelar
                    </button>
                </div>
                @error('emailRecipient')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </form>
        </div>
    @endif

    {{-- Signos Vitales --}}
    @if ($record->weight || $record->height || $record->blood_pressure || $record->temperature || $record->heart_rate)
        <div class="mb-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-3">Signos Vitales</h4>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                @if ($record->weight)
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <p class="text-xs text-gray-600">Peso</p>
                        <p class="text-lg font-bold text-gray-900">{{ $record->weight }} kg</p>
                    </div>
                @endif

                @if ($record->height)
                    <div class="bg-green-50 p-3 rounded-lg">
                        <p class="text-xs text-gray-600">Altura</p>
                        <p class="text-lg font-bold text-gray-900">{{ $record->height }} cm</p>
                    </div>
                @endif

                @if ($record->blood_pressure)
                    <div class="bg-purple-50 p-3 rounded-lg">
                        <p class="text-xs text-gray-600">Presión Arterial</p>
                        <p class="text-lg font-bold text-gray-900">{{ $record->blood_pressure }}</p>
                    </div>
                @endif

                @if ($record->temperature)
                    <div class="bg-yellow-50 p-3 rounded-lg">
                        <p class="text-xs text-gray-600">Temperatura</p>
                        <p class="text-lg font-bold text-gray-900">{{ $record->temperature }}°C</p>
                    </div>
                @endif

                @if ($record->heart_rate)
                    <div class="bg-red-50 p-3 rounded-lg">
                        <p class="text-xs text-gray-600">Frecuencia Cardíaca</p>
                        <p class="text-lg font-bold text-gray-900">{{ $record->heart_rate }} bpm</p>
                    </div>
                @endif
            </div>

            @if ($record->bmi)
                <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600">
                        <strong>IMC (Índice de Masa Corporal):</strong> {{ $record->bmi }}
                        @if ($record->bmi < 18.5)
                            <span class="text-blue-600">(Bajo peso)</span>
                        @elseif($record->bmi < 25)
                            <span class="text-green-600">(Normal)</span>
                        @elseif($record->bmi < 30)
                            <span class="text-yellow-600">(Sobrepeso)</span>
                        @else
                            <span class="text-red-600">(Obesidad)</span>
                        @endif
                    </p>
                </div>
            @endif
        </div>
    @endif

    {{-- Información Clínica (Desencriptada) --}}
    <div class="space-y-6">
        @if ($record->chief_complaint)
            <div>
                <h4 class="text-sm font-semibold text-gray-700 mb-2">Motivo de Consulta</h4>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-900">{{ $record->chief_complaint }}</p>
                </div>
            </div>
        @endif

        <div>
            <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                        clip-rule="evenodd" />
                </svg>
                Síntomas (Encriptado)
            </h4>
            <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                <p class="text-gray-900 whitespace-pre-wrap">{{ $record->symptoms }}</p>
            </div>
        </div>

        <div>
            <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                        clip-rule="evenodd" />
                </svg>
                Diagnóstico (Encriptado)
            </h4>
            <div class="bg-red-50 border border-red-200 p-4 rounded-lg">
                <p class="text-gray-900 whitespace-pre-wrap">{{ $record->diagnosis }}</p>
            </div>
        </div>

        @if ($record->treatment_plan)
            <div>
                <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                            clip-rule="evenodd" />
                    </svg>
                    Plan de Tratamiento (Encriptado)
                </h4>
                <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                    <p class="text-gray-900 whitespace-pre-wrap">{{ $record->treatment_plan }}</p>
                </div>
            </div>
        @endif

        @if ($record->prescriptions)
            <div>
                <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                            clip-rule="evenodd" />
                    </svg>
                    Prescripciones / Medicamentos (Encriptado)
                </h4>
                <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                    <p class="text-gray-900 whitespace-pre-wrap">{{ $record->prescriptions }}</p>
                </div>
            </div>
        @endif

        <div>
            <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                        clip-rule="evenodd" />
                </svg>
                Notas Clínicas (Encriptado)
            </h4>
            <div class="bg-purple-50 border border-purple-200 p-4 rounded-lg">
                <p class="text-gray-900 whitespace-pre-wrap">{{ $record->clinical_notes }}</p>
            </div>
        </div>
    </div>

    {{-- Footer con info de encriptación --}}
    <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
        <p class="text-sm text-green-800 flex items-start gap-2">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
            </svg>
            <span>
                <strong>Seguridad:</strong> Todos los datos clínicos mostrados están encriptados en la base de datos
                usando AES-256-CBC.
                Solo usuarios autorizados de esta clínica pueden verlos desencriptados.
            </span>
        </p>
    </div>

    {{-- Botón Cerrar --}}
    <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
        <button wire:click="$dispatch('closeModal')"
            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            Cerrar
        </button>
    </div>
</div>
