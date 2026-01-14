<?php

declare(strict_types=1);

namespace App\Livewire\Patients;

use App\Models\Patient;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class Edit extends Component
{
    use AuthorizesRequests;

    public Patient $patient;

    // Propiedades del formulario
    public string $first_name = '';
    public string $last_name = '';
    public string $identification_type = '';
    public string $identification_number = '';
    public string $date_of_birth = '';
    public string $gender = '';
    public string $blood_type = '';
    public string $email = '';
    public string $phone = '';
    public string $mobile_phone = '';
    public string $address = '';
    public string $city = '';
    public string $emergency_contact_name = '';
    public string $emergency_contact_phone = '';
    public string $emergency_contact_relationship = '';
    public string $notes = '';

    /**
     * Mount - Cargar datos del paciente
     */
    public function mount(int $patientId): void
    {
        $this->patient = Patient::findOrFail($patientId);
        $this->authorize('update', $this->patient);

        // Cargar datos actuales
        $this->first_name = $this->patient->first_name;
        $this->last_name = $this->patient->last_name;
        $this->identification_type = $this->patient->identification_type;
        $this->identification_number = $this->patient->identification_number;
        $this->date_of_birth = $this->patient->date_of_birth->format('Y-m-d');
        $this->gender = $this->patient->gender ?? '';
        $this->blood_type = $this->patient->blood_type ?? '';
        $this->email = $this->patient->email ?? '';
        $this->phone = $this->patient->phone;
        $this->mobile_phone = $this->patient->mobile_phone ?? '';
        $this->address = $this->patient->address ?? '';
        $this->city = $this->patient->city ?? '';
        $this->emergency_contact_name = $this->patient->emergency_contact_name ?? '';
        $this->emergency_contact_phone = $this->patient->emergency_contact_phone ?? '';
        $this->emergency_contact_relationship = $this->patient->emergency_contact_relationship ?? '';
        $this->notes = $this->patient->notes ?? '';
    }

    /**
     * Reglas de validación
     */
    protected function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'identification_type' => 'required|string|in:CC,TI,CE,PP',
            'identification_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('patients', 'identification_number')->ignore($this->patient->id),
            ],
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'nullable|in:M,F,O',
            'blood_type' => 'nullable|string|max:10',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'mobile_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Mensajes de validación
     */
    protected function messages(): array
    {
        return [
            'first_name.required' => 'El nombre es obligatorio',
            'last_name.required' => 'El apellido es obligatorio',
            'identification_number.required' => 'El número de documento es obligatorio',
            'identification_number.unique' => 'Ya existe un paciente con este documento',
            'date_of_birth.required' => 'La fecha de nacimiento es obligatoria',
            'date_of_birth.before' => 'La fecha de nacimiento debe ser anterior a hoy',
            'phone.required' => 'El teléfono es obligatorio',
            'email.email' => 'El email no tiene un formato válido',
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
     * Actualizar paciente
     */
    public function save(): void
    {
        $this->authorize('update', $this->patient);

        $validated = $this->validate();

        $this->patient->update($validated);

        // Emitir eventos
        $this->dispatch('patientUpdated');
        $this->dispatch('closeModal');

        session()->flash('message', 'Paciente actualizado correctamente');
    }

    /**
     * Renderizar componente
     */
    public function render(): View
    {
        return view('livewire.patients.edit');
    }
}