<?php

declare(strict_types=1);

namespace App\Livewire\MedicalRecords;

use App\Enums\MedicalRecordType;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Component;

class Create extends Component
{
    use AuthorizesRequests;

    public Patient $patient;

    // Propiedades del formulario
    public string $record_type = 'consultation';
    public string $chief_complaint = '';
    public string $symptoms = '';
    public string $diagnosis = '';
    public string $treatment_plan = '';
    public string $prescriptions = '';
    public string $clinical_notes = '';
    public string $consultation_date = '';
    public string $weight = '';
    public string $height = '';
    public string $blood_pressure = '';
    public string $temperature = '';
    public string $heart_rate = '';

    /**
     * Mount
     */
    public function mount(int $patientId): void
    {
        $this->patient = Patient::findOrFail($patientId);
        $this->authorize('viewMedicalRecords', $this->patient);
        
        // Fecha por defecto: hoy
        $this->consultation_date = now()->format('Y-m-d');
    }

    /**
     * Reglas de validación
     */
    protected function rules(): array
    {
        return [
            'record_type' => 'required|in:' . implode(',', MedicalRecordType::values()),
            'chief_complaint' => 'nullable|string|max:500',
            'symptoms' => 'required|string|max:2000',
            'diagnosis' => 'required|string|max:2000',
            'treatment_plan' => 'nullable|string|max:2000',
            'prescriptions' => 'nullable|string|max:2000',
            'clinical_notes' => 'required|string|max:5000',
            'consultation_date' => 'required|date|before_or_equal:today',
            'weight' => 'nullable|numeric|min:0|max:500',
            'height' => 'nullable|numeric|min:0|max:300',
            'blood_pressure' => 'nullable|string|max:20',
            'temperature' => 'nullable|numeric|min:30|max:45',
            'heart_rate' => 'nullable|integer|min:30|max:250',
        ];
    }

    /**
     * Mensajes de validación
     */
    protected function messages(): array
    {
        return [
            'symptoms.required' => 'Los síntomas son obligatorios',
            'diagnosis.required' => 'El diagnóstico es obligatorio',
            'clinical_notes.required' => 'Las notas clínicas son obligatorias',
            'consultation_date.required' => 'La fecha de consulta es obligatoria',
            'consultation_date.before_or_equal' => 'La fecha no puede ser futura',
            'weight.numeric' => 'El peso debe ser un número',
            'height.numeric' => 'La altura debe ser un número',
            'temperature.between' => 'La temperatura debe estar entre 30 y 45°C',
        ];
    }

    /**
     * Validación en tiempo real
     */
    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    /**
     * Guardar registro médico
     */
    public function save(): void
    {
        $this->authorize('create', MedicalRecord::class);

        $validated = $this->validate();

        // Agregar campos adicionales
        $validated['patient_id'] = $this->patient->id;
        $validated['created_by'] = auth()->user()->id;

        MedicalRecord::create($validated);

        $this->dispatch('medicalRecordCreated');
        $this->dispatch('closeModal');

        session()->flash('message', 'Registro médico creado correctamente');
    }

    /**
     * Renderizar componente
     */
    public function render(): View
    {
        return view('livewire.medical-records.create', [
            'recordTypes' => MedicalRecordType::options(),
        ]);
    }
}