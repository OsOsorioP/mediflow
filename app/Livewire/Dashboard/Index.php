<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    public string $period = 'month';

    public function setPeriod(string $period): void
    {
        $this->period = $period;
    }

    public function render(): View
    {

        [$startDate, $endDate] = $this->getDateRange();

        $stats = [
            'total_patients' => Patient::active()->count(),
            
            'total_appointments' => Appointment::whereBetween('scheduled_at', [$startDate, $endDate])
                ->count(),
            
            'completed_appointments' => Appointment::whereBetween('scheduled_at', [$startDate, $endDate])
                ->where('status', AppointmentStatus::COMPLETED)
                ->count(),
            
            'pending_appointments' => Appointment::where('scheduled_at', '>=', now())
                ->whereIn('status', AppointmentStatus::activeStatuses())
                ->count(),
            
            'total_medical_records' => MedicalRecord::whereBetween('consultation_date', [$startDate, $endDate])
                ->count(),
            
            'new_patients_this_period' => Patient::whereBetween('created_at', [$startDate, $endDate])
                ->count(),
        ];

        $todayAppointments = Appointment::with(['patient', 'doctor'])
            ->whereDate('scheduled_at', today())
            ->orderBy('scheduled_at')
            ->get();

        $upcomingAppointments = Appointment::with(['patient', 'doctor'])
            ->whereBetween('scheduled_at', [now(), now()->addDays(7)])
            ->whereIn('status', AppointmentStatus::activeStatuses())
            ->orderBy('scheduled_at')
            ->limit(10)
            ->get();

        $appointmentsByStatus = Appointment::whereBetween('scheduled_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                $label = method_exists($item->status, 'label') ? $item->status->label() : $item->status->value;
                return [$label => $item->count];
            });

        $topPatients = Patient::query()
            ->withCount(['appointments' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('scheduled_at', [$startDate, $endDate]);
            }])
            ->whereHas('appointments', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('scheduled_at', [$startDate, $endDate]);
            })
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

    protected function getDateRange(): array
    {
        $now = Carbon::now();
        return match($this->period) {
            'week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }
    
}