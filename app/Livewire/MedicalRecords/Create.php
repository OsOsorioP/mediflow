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
/**
 * Componente para la creación de historiales médicos.
 * 
 * Este componente permite registrar diferentes tipos de antecedentes
 * médicos para un paciente específico, incluyendo consultas,
 * diagnósticos y tratamientos.
 */
class Create extends Component
{
    /**
     * El paciente al cual se le creará el registro médico.
     * @var Patient
     */
    public Patient $patient;

    // Propiedades del formulario

    /**
     * Tipo de registro médico (ej. consulta, examen, etc.)
     * @var string
     */
    public string $record_type = 'consultation';

    /**
     * Motivo principal de la consulta.
     * @var string
     */
    public string $chief_complaint = '';

    /**
     * Descripción detallada de los síntomas presentados.
     * @var string
     */
    public string $symptoms = '';

    /**
     * Diagnóstico médico realizado.
     * @var string
     */
    public string $diagnosis = '';

    /**
     * Plan de tratamiento propuesto.
     * @var string
     */
    public string $treatment_plan = '';

    /**
     * Medicamentos recetados.
     * @var string
     */
    public string $prescriptions = '';

    /**
     * Notas clínicas adicionales.
     * @var string
     */
    public string $clinical_notes = '';

    /**
     * Fecha de la consulta.
     * @var string
     */
    public string $consultation_date = '';

    /**
     * Peso del paciente en kg.
     * @var float|null
     */
    public ?float $weight = null;

    /**
     * Altura del paciente en cm.
     * @var float|null
     */
    public ?float $height = null;

    /**
     * Presión arterial (ej. 120/80).
     * @var string
     */
    public string $blood_pressure = '';

    /**
     * Temperatura corporal en grados Celsius.
     * @var float|null
     */
    public ?float $temperature = null;

    /**
     * Frecuencia cardíaca en latidos por minuto.
     * @var int|null
     */
    public ?int $heart_rate = null;

    /**
     * Inicializa el componente.
     * 
     * @param Patient $patient El modelo del paciente inyectado automáticamente.
     */
    public function mount(Patient $patient): void
    {
        // El Global Scope ya protege que el paciente sea de la clínica correcta
        $this->patient = $patient;
        $this->consultation_date = now()->format('Y-m-d');
    }

    /**
     * Define las reglas de validación para las propiedades del formulario.
     * 
     * @return array<string, string> Reglas de validación.
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
     * Guarda el nuevo registro médico en la base de datos.
     * 
     * Realiza la validación, asignación de datos automáticos (usuario, paciente)
     * y crea el registro. Redirige al perfil del paciente tras el éxito.
     * 
     * @return mixed Redirección a la ruta del paciente.
     */
    public function save()
    {
        $this->authorize('create', MedicalRecord::class);
        $validated = $this->validate();

        // Asignaciones manuales necesarias
        $validated['patient_id'] = $this->patient->id;
        $validated['created_by'] = Auth::id(); // Uso de Facade (Senior Practice)

        // El clinic_id se asigna solo gracias al Trait MultiTenant
        $record = MedicalRecord::create($validated);

        session()->flash('message', 'Registro médico guardado correctamente.');

        // Redirigimos de vuelta al perfil del paciente, pestaña de registros
        return $this->redirect(route('patients.show', $this->patient), navigate: true);
    }

    /**
     * Renderiza la vista del componente.
     * 
     * @return View La vista de Livewire.
     */
    public function render(): View
    {
        return view('livewire.medical-records.create', [
            'recordTypes' => MedicalRecordType::options(),
        ]);
    }
}
