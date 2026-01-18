<?php

declare(strict_types=1);

namespace App\Livewire\Payments;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Payment;
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
    public string $appointment_id = '';
    public string $amount = '';
    public string $currency = 'USD';
    public string $payment_method = 'cash';
    public string $status = 'completed';
    public string $concept = '';
    public string $description = '';
    public string $reference_number = '';
    public string $payment_date = '';

    // Opciones
    public array $appointments = [];

    /**
     * Mount
     */
    public function mount(?int $patientId = null, ?int $appointmentId = null): void
    {
        $this->payment_date = today()->format('Y-m-d');

        if ($patientId) {
            $this->patient_id = (string) $patientId;
            $this->loadAppointments();
        }

        if ($appointmentId) {
            $this->appointment_id = (string) $appointmentId;
            $appointment = Appointment::find($appointmentId);
            if ($appointment) {
                $this->patient_id = (string) $appointment->patient_id;
                $this->concept = 'Consulta - ' . $appointment->appointment_type?->label();
            }
        }
    }

    /**
     * Reglas de validación
     */
    protected function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|in:USD,COP,EUR,MXN',
            'payment_method' => 'required|in:' . implode(',', array_column(PaymentMethod::cases(), 'value')),
            'status' => 'required|in:' . implode(',', array_column(PaymentStatus::cases(), 'value')),
            'concept' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'reference_number' => 'nullable|string|max:255',
            'payment_date' => 'required|date|before_or_equal:today',
        ];
    }

    /**
     * Mensajes de validación
     */
    protected function messages(): array
    {
        return [
            'patient_id.required' => 'Debe seleccionar un paciente',
            'amount.required' => 'El monto es obligatorio',
            'amount.min' => 'El monto debe ser mayor a 0',
            'payment_method.required' => 'El método de pago es obligatorio',
            'concept.required' => 'El concepto es obligatorio',
            'payment_date.required' => 'La fecha de pago es obligatoria',
            'payment_date.before_or_equal' => 'La fecha no puede ser futura',
        ];
    }

    /**
     * Validación en tiempo real
     */
    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);

        // Cargar citas cuando cambia el paciente
        if ($propertyName === 'patient_id') {
            $this->loadAppointments();
        }
    }

    /**
     * Cargar citas del paciente
     */
    public function loadAppointments(): void
    {
        if (!$this->patient_id) {
            $this->appointments = [];
            return;
        }

        $this->appointments = Appointment::where('patient_id', $this->patient_id)
            ->whereIn('status', \App\Enums\AppointmentStatus::activeStatuses())
            ->orderBy('scheduled_at', 'desc')
            ->get()
            ->map(fn($apt) => [
                'id' => $apt->id,
                'label' => $apt->scheduled_at->format('d/m/Y H:i') . ' - ' . $apt->appointment_type?->label(),
            ])
            ->toArray();
    }

    /**
     * Guardar pago
     */
    public function save(): void
    {
        $this->authorize('create', Payment::class);

        $validated = $this->validate();

        $payment = Payment::create([
            'patient_id' => $validated['patient_id'],
            'appointment_id' => $validated['appointment_id'] ?: null,
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'payment_method' => $validated['payment_method'],
            'status' => $validated['status'],
            'concept' => $validated['concept'],
            'description' => $validated['description'] ?: null,
            'reference_number' => $validated['reference_number'] ?: null,
            'payment_date' => $validated['payment_date'],
            'created_by' => auth()->id(),
        ]);

        $this->dispatch('paymentCreated');
        $this->dispatch('closeModal');

        session()->flash('message', 'Pago registrado correctamente - #' . $payment->payment_number);
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

        return view('livewire.payments.create', [
            'patients' => $patients,
            'paymentMethods' => PaymentMethod::options(),
            'paymentStatuses' => PaymentStatus::options(),
            'currencies' => [
                'USD' => 'Dólar (USD)',
                'COP' => 'Peso Colombiano (COP)',
                'EUR' => 'Euro (EUR)',
                'MXN' => 'Peso Mexicano (MXN)',
            ],
        ]);
    }
}
