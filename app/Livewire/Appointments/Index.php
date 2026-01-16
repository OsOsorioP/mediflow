<?php

declare(strict_types=1);

namespace App\Livewire\Appointments;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination, AuthorizesRequests;

    // Filtros
    public string $filterDate = '';
    public string $filterStatus = '';
    public string $filterDoctor = '';
    public string $search = '';
    public string $viewMode = 'list'; // list, calendar

    // Listeners
    protected $listeners = [
        'appointmentCreated' => '$refresh',
        'appointmentUpdated' => '$refresh',
    ];

    /**
     * Mount
     */
    public function mount(): void
    {
        // Por defecto, mostrar citas de hoy
        $this->filterDate = today()->format('Y-m-d');
    }

    /**
     * Resetear paginación cuando cambian los filtros
     */
    public function updatedFilterDate(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterDoctor(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Cambiar vista
     */
    public function setViewMode(string $mode): void
    {
        $this->viewMode = $mode;
    }

    /**
     * Confirmar cita
     */
    public function confirmAppointment(int $appointmentId): void
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $this->authorize('confirm', $appointment);

        $appointment->confirm();
        session()->flash('message', 'Cita confirmada');
    }

    /**
     * Completar cita
     */
    public function completeAppointment(int $appointmentId): void
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $this->authorize('complete', $appointment);

        $appointment->complete();
        session()->flash('message', 'Cita marcada como completada');
    }

    /**
     * Cancelar cita
     */
    public function cancelAppointment(int $appointmentId, string $reason = ''): void
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $this->authorize('cancel', $appointment);

        $appointment->cancel($reason ?: 'Sin motivo especificado');
        session()->flash('message', 'Cita cancelada');
    }

    /**
     * Marcar como no asistió
     */
    public function markAsNoShow(int $appointmentId): void
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $this->authorize('complete', $appointment);

        $appointment->markAsNoShow();
        session()->flash('message', 'Cita marcada como no asistió');
    }

    /**
     * Renderizar
     */
    public function render(): View
    {
        $this->authorize('viewAny', Appointment::class);

        // Query base
        $query = Appointment::query()
            ->with(['patient', 'doctor', 'creator']);

        // Filtrar por fecha
        if ($this->filterDate) {
            $query->whereDate('scheduled_at', $this->filterDate);
        }

        // Filtrar por estado
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        // Filtrar por médico
        if ($this->filterDoctor) {
            $query->where('user_id', $this->filterDoctor);
        }

        // Búsqueda por nombre de paciente
        if ($this->search) {
            $query->whereHas('patient', function ($q) {
                $q->where('first_name', 'ilike', "%{$this->search}%")
                  ->orWhere('last_name', 'ilike', "%{$this->search}%");
            });
        }

        // Ordenar
        $query->orderBy('scheduled_at', 'asc');

        $appointments = $query->paginate(20);

        // Obtener médicos para el filtro
        $doctors = User::where('clinic_id', auth()->user()->clinic_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Estadísticas del día
        $todayStats = $this->getTodayStats();

        return view('livewire.appointments.index', [
            'appointments' => $appointments,
            'doctors' => $doctors,
            'statuses' => AppointmentStatus::options(),
            'todayStats' => $todayStats,
        ]);
    }

    /**
     * Obtener estadísticas del día seleccionado
     */
    protected function getTodayStats(): array
    {
        $date = $this->filterDate ? Carbon::parse($this->filterDate) : today();

        $query = Appointment::query()
            ->whereDate('scheduled_at', $date);

        return [
            'total' => $query->count(),
            'pending' => (clone $query)->where('status', AppointmentStatus::PENDING)->count(),
            'confirmed' => (clone $query)->where('status', AppointmentStatus::CONFIRMED)->count(),
            'completed' => (clone $query)->where('status', AppointmentStatus::COMPLETED)->count(),
            'cancelled' => (clone $query)->where('status', AppointmentStatus::CANCELLED)->count(),
        ];
    }
}