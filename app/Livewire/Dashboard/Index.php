<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class Index extends Component
{
    public string $period = 'month'; // week, month, year

    /**
     * Cambiar período
     */
    public function setPeriod(string $period): void
    {
        $this->period = $period;
    }

    /**
     * Renderizar
     */
    public function render(): View
    {
        $clinicId = auth()->user()->clinic_id;

        // Obtener rangos de fechas según período
        [$startDate, $endDate] = $this->getDateRange();

        // KPIs principales
        $stats = [
            'total_patients' => Patient::where('clinic_id', $clinicId)
                ->where('is_active', true)
                ->count(),
            
            'total_appointments' => Appointment::where('clinic_id', $clinicId)
                ->whereBetween('scheduled_at', [$startDate, $endDate])
                ->count(),
            
            'completed_appointments' => Appointment::where('clinic_id', $clinicId)
                ->whereBetween('scheduled_at', [$startDate, $endDate])
                ->where('status', AppointmentStatus::COMPLETED)
                ->count(),
            
            'pending_appointments' => Appointment::where('clinic_id', $clinicId)
                ->where('scheduled_at', '>=', now())
                ->whereIn('status', AppointmentStatus::activeStatuses())
                ->count(),
            
            'total_medical_records' => MedicalRecord::where('clinic_id', $clinicId)
                ->whereBetween('consultation_date', [$startDate, $endDate])
                ->count(),
            
            'new_patients_this_period' => Patient::where('clinic_id', $clinicId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
        ];

        // Citas de hoy
        $todayAppointments = Appointment::where('clinic_id', $clinicId)
            ->with(['patient', 'doctor'])
            ->whereDate('scheduled_at', today())
            ->orderBy('scheduled_at')
            ->get();

        // Próximas citas (7 días)
        $upcomingAppointments = Appointment::where('clinic_id', $clinicId)
            ->with(['patient', 'doctor'])
            ->whereBetween('scheduled_at', [now(), now()->addDays(7)])
            ->whereIn('status', AppointmentStatus::activeStatuses())
            ->orderBy('scheduled_at')
            ->limit(10)
            ->get();

        // Distribución de citas por estado (para gráfica)
        $appointmentsByStatus = Appointment::where('clinic_id', $clinicId)
            ->whereBetween('scheduled_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status->label() => $item->count];
            });

        // Pacientes más frecuentes
        $topPatients = Patient::where('clinic_id', $clinicId)
            ->withCount([
                'appointments' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('scheduled_at', [$startDate, $endDate]);
                }
            ])
            ->having('appointments_count', '>', 0)
            ->orderByDesc('appointments_count')
            ->limit(5)
            ->get();

        return view('livewire.dashboard.index', [
            'stats' => $stats,
            'todayAppointments' => $todayAppointments,
            'upcomingAppointments' => $upcomingAppointments,
            'appointmentsByStatus' => $appointmentsByStatus,
            'topPatients' => $topPatients,
        ]);
    }

    /**
     * Obtener rango de fechas según el período
     */
    protected function getDateRange(): array
    {
        return match($this->period) {
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }
}