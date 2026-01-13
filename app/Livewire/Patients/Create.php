<?php

declare(strict_types=1);

namespace App\Livewire\Patients;

use App\Models\Patient;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Component;

class Create extends Component
{
    use AuthorizesRequests;

    // Propiedades del formulario
    public string $first_name = '';
    public string $last_name = '';
    public string $identification_type = 'CC';
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
     * Reglas de validación
     */
    protected function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'identification_type' => 'required|string|in:CC,TI,CE,PP',
            'identification_number' => 'required|string|max:255|unique:patients,identification_number',
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
     * Mensajes de validación personalizados
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
     * Guardar paciente
     */
    public function save(): void
    {
        $this->authorize('create', Patient::class);

        $validated = $this->validate();

        $patient = Patient::create($validated);

        // Emitir evento para que el componente Index se actualice
        $this->dispatch('patientCreated');
        $this->dispatch('closeModal');

        session()->flash('message', 'Paciente creado correctamente');
        
        // Redirigir a la vista del paciente
        $this->redirect(route('patients.show', $patient), navigate: true);
    }

    /**
     * Renderizar componente
     */
    public function render(): View
    {
        return view('livewire.patients.create');
    }
}