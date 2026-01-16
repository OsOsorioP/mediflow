<?php

declare(strict_types=1);

namespace App\Livewire\Appointments;

use App\Actions\Appointments\ScheduleAppointmentAction;
use App\Enums\AppointmentType;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Create extends Component 
{
    use AuthorizesRequests;

    // Propiedades del formulario
    public string $patient_id = '';
    public string $user_id = '';
    public string $appointment_date = '';
    public string $appointment_time = '';
    public string $duration_minutes = '30';
    public string $appointment_type = 'consultation';
    public string $reason = '';
    public string $notes = '';

    // Slots disponibles
    public array $availableSlots = [];

    /**
     * Mount
     */
    public function mount(): void
    {
        $this->appointment_date = today()->format('Y-m-d');
        
        // Si solo hay un médico, seleccionarlo por defecto
        $doctors = User::where('clinic_id', auth()->user()->clinic_id)
            ->where('is_active', true)
            ->get();
        
        if ($doctors->count() === 1) {
            $this->user_id = (string) $doctors->first()->id;
            $this->loadAvailableSlots();
        }
    }

    /**
     * Reglas de validación
     */
    protected function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'user_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'duration_minutes' => 'required|integer|min:10|max:240',
            'appointment_type' => 'nullable|string',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Mensajes personalizados
     */
    protected function messages(): array
    {
        return [
            'patient_id.required' => 'Debe seleccionar un paciente',
            'user_id.required' => 'Debe seleccionar un médico',
            'appointment_date.required' => 'La fecha es obligatoria',
            'appointment_date.after_or_equal' => 'No se pueden agendar citas en el pasado',
            'appointment_time.required' => 'La hora es obligatoria',
            'duration_minutes.required' => 'La duración es obligatoria',
        ];
    }

    /**
     * Validación en tiempo real
     */
    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
        
        // Recargar slots cuando cambie fecha o médico
        if (in_array($propertyName, ['appointment_date', 'user_id', 'duration_minutes'])) {
            $this->loadAvailableSlots();
        }
    }

    /**
     * Cargar slots disponibles
     */
    public function loadAvailableSlots(): void
    {
        if (!$this->user_id || !$this->appointment_date) {
            $this->availableSlots = [];
            return;
        }

        try {
            $action = new ScheduleAppointmentAction();
            $date = Carbon::parse($this->appointment_date);
            $duration = (int) ($this->duration_minutes ?: 30);
            
            $this->availableSlots = $action->getAvailableSlots(
                (int) $this->user_id,
                $date,
                $duration
            );
        } catch (\Exception $e) {
            $this->availableSlots = [];
        }
    }

    /**
     * Seleccionar un slot
     */
    public function selectSlot(string $time): void
    {
        $this->appointment_time = $time;
    }

    /**
     * Guardar cita
     */
    public function save(): void
    {
        $this->authorize('create', Appointment::class);

        $validated = $this->validate();

        try {
            // Combinar fecha y hora
            $scheduledAt = Carbon::parse("{$this->appointment_date} {$this->appointment_time}");

            $action = new ScheduleAppointmentAction();
            $appointment = $action->execute([
                'patient_id' => $this->patient_id,
                'user_id' => $this->user_id,
                'scheduled_at' => $scheduledAt,
                'duration_minutes' => $this->duration_minutes,
                'appointment_type' => $this->appointment_type,
                'reason' => $this->reason,
                'notes' => $this->notes,
            ]);

            $this->dispatch('appointmentCreated');
            $this->dispatch('closeModal');

            session()->flash('message', 'Cita agendada correctamente. Se enviará un email de confirmación al paciente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Propagar errores de validación de la Action
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        }
    }

    /**
     * Renderizar
     */
    public function render(): View
    {
        $patients = Patient::where('clinic_id', auth()->user()->clinic_id)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();

        $doctors = User::where('clinic_id', auth()->user()->clinic_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.appointments.create', [
            'patients' => $patients,
            'doctors' => $doctors,
            'appointmentTypes' => AppointmentType::options(),
        ]);
    }
}