<?php

declare(strict_types=1);

namespace App\Livewire\Appointments;

use App\Actions\Appointments\ScheduleAppointmentAction;
use App\Enums\AppointmentStatus;
use App\Enums\AppointmentType;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Edit extends Component
{
    use AuthorizesRequests;

    public Appointment $appointment;

    public string $patient_id = '';
    public string $user_id = '';
    public string $appointment_date = '';
    public string $appointment_time = '';
    public string $duration_minutes = '30';
    public string $appointment_type = 'consultation';
    public string $reason = '';
    public string $notes = '';

    public array $availableSlots = [];

    public function mount(Appointment $appointment): void
    {
        $this->authorize('update', $appointment);

        $this->appointment = $appointment;

        $this->patient_id = (string) $appointment->patient_id;
        $this->user_id = (string) $appointment->user_id;
        $this->appointment_date = $appointment->scheduled_at->format('Y-m-d');
        $this->appointment_time = $appointment->scheduled_at->format('H:i');
        $this->duration_minutes = (string) $appointment->duration_minutes;
        $this->appointment_type = $appointment->appointment_type->value ?? 'consultation';
        $this->reason = $appointment->reason ?? '';
        $this->notes = $appointment->notes ?? '';

        $this->loadAvailableSlots();
    }

    protected function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'user_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date', // Permitir fechas pasadas si ya es una cita pasada? Asumimos que no se editan citas pasadas para moverlas al futuro sin validación, pero por ahora simple.
            'appointment_time' => 'required',
            'duration_minutes' => 'required|integer|min:10|max:240',
            'appointment_type' => 'nullable|string',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    protected function messages(): array
    {
        return [
            'patient_id.required' => 'Debe seleccionar un paciente',
            'user_id.required' => 'Debe seleccionar un médico',
            'appointment_date.required' => 'La fecha es obligatoria',
            'appointment_time.required' => 'La hora es obligatoria',
            'duration_minutes.required' => 'La duración es obligatoria',
        ];
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);

        if (in_array($propertyName, ['appointment_date', 'user_id', 'duration_minutes'])) {
            $this->loadAvailableSlots();
        }
    }

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

            if (
                (int)$this->user_id === $this->appointment->user_id &&
                $this->appointment_date === $this->appointment->scheduled_at->format('Y-m-d')
            ) {
                $currentSlot = $this->appointment->scheduled_at->format('H:i');
                if (!in_array($currentSlot, $this->availableSlots)) {
                    $this->availableSlots[] = $currentSlot;
                    sort($this->availableSlots);
                }
            }
        } catch (\Exception $e) {
            $this->availableSlots = [];
        }
    }

    public function selectSlot(string $time): void
    {
        $this->appointment_time = $time;
    }

    public function save(): void
    {
        $this->authorize('update', $this->appointment);

        $validated = $this->validate();

        try {
            $scheduledAt = Carbon::parse("{$this->appointment_date} {$this->appointment_time}");

            $this->appointment->update([
                'patient_id' => $this->patient_id,
                'user_id' => $this->user_id,
                'scheduled_at' => $scheduledAt,
                'duration_minutes' => $this->duration_minutes,
                'appointment_type' => $this->appointment_type,
                'reason' => $this->reason,
                'notes' => $this->notes,
            ]);

            $this->dispatch('appointmentUpdated');
            $this->dispatch('closeModal');

            session()->flash('message', 'Cita actualizada correctamente.');
        } catch (\Exception $e) {
            $this->addError('base', 'Error al actualizar la cita: ' . $e->getMessage());
        }
    }

    public function render(): View
    {
        $user = auth()->user();

        $patients = Patient::where('clinic_id', $user->clinic_id)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();

        $doctors = User::where('clinic_id', $user->clinic_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.appointments.edit', [
            'patients' => $patients,
            'doctors' => $doctors,
            'appointmentTypes' => AppointmentType::options(),
        ]);
    }
}
