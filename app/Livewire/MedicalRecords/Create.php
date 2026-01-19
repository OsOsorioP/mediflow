<?php

declare(strict_types=1);

namespace App\Livewire\MedicalRecords;

use App\Enums\MedicalRecordType;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Create extends Component
{
    public Patient $patient;

    public string $record_type = 'consultation';

    public string $chief_complaint = '';

    public string $symptoms = '';

    public string $diagnosis = '';

    public string $treatment_plan = '';

    public string $prescriptions = '';

    public string $clinical_notes = '';
    public string $consultation_date = '';
    public ?float $weight = null;

    public ?float $height = null;

    public string $blood_pressure = '';

    public ?float $temperature = null;

    public ?int $heart_rate = null;

    public function mount(Patient $patient): void
    {
        $this->patient = $patient;
        $this->consultation_date = now()->format('Y-m-d');
    }

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

    public function save()
    {
        $this->authorize('create', MedicalRecord::class);
        $validated = $this->validate();

        $validated['patient_id'] = $this->patient->id;
        $validated['created_by'] = Auth::id();

        $record = MedicalRecord::create($validated);

        session()->flash('message', 'Registro médico guardado correctamente.');

        return $this->redirect(route('patients.show', $this->patient), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.medical-records.create', [
            'recordTypes' => MedicalRecordType::options(),
        ]);
    }
}
