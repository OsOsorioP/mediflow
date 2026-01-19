<?php

declare(strict_types=1);

namespace App\Livewire\Patients;

use App\Models\Patient;
use App\Services\TenantManager;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')] // Aseguramos el layout
class Edit extends Component
{
    public Patient $patient;

    // Propiedades del formulario
    public string $first_name = '';
    public string $last_name = '';
    public string $identification_type = '';
    public string $identification_number = '';
    public string $date_of_birth = '';
    public ?string $gender = '';
    public ?string $blood_type = '';
    public ?string $email = '';
    public string $phone = '';
    public ?string $mobile_phone = '';
    public ?string $address = '';
    public ?string $city = '';
    public ?string $emergency_contact_name = '';
    public ?string $emergency_contact_phone = '';
    public ?string $emergency_contact_relationship = '';
    public ?string $notes = '';

    public function mount(Patient $patient): void
    {
        $this->authorize('update', $patient);
        $this->patient = $patient;

        $this->fill($patient->toArray());

        $this->date_of_birth = $patient->date_of_birth->format('Y-m-d');
    }

    protected function rules(): array
    {
        $clinicId = app(TenantManager::class)->getClinicId();

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'identification_type' => 'required|string|in:CC,TI,CE,PP',
            'identification_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('patients')
                    ->where(fn($q) => $q->where('clinic_id', $clinicId))
                    ->ignore($this->patient->id),
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

    public function save()
    {
        $this->authorize('update', $this->patient);
        $validated = $this->validate();

        $this->patient->update($validated);

        session()->flash('message', 'Paciente actualizado correctamente');

        return $this->redirect(route('patients.show', $this->patient), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.patients.edit');
    }
}
