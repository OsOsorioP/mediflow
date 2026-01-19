<?php

declare(strict_types=1);

namespace App\Livewire\Payments;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination, AuthorizesRequests;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterMethod = '';
    public string $filterDateFrom = '';
    public string $filterDateTo = '';
    public string $period = 'month';

    protected $listeners = [
        'paymentCreated' => '$refresh',
        'paymentUpdated' => '$refresh',
    ];

    public function mount(): void
    {
        $this->setPeriod('month');
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;
        
        [$start, $end] = match($period) {
            'today' => [today(), today()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            'custom' => [null, null],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };

        if ($start && $end) {
            $this->filterDateFrom = $start->format('Y-m-d');
            $this->filterDateTo = $end->format('Y-m-d');
        }

        $this->resetPage();
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['search', 'filterStatus', 'filterMethod', 'filterDateFrom', 'filterDateTo'])) {
            $this->resetPage();
        }
    }

    public function cancelPayment(int $paymentId): void
    {
        $payment = Payment::findOrFail($paymentId);
        $this->authorize('cancel', $payment);

        $payment->cancel('Cancelado por usuario');
        session()->flash('message', 'Pago cancelado correctamente');
    }

    public function render(): View
    {
        $this->authorize('viewAny', Payment::class);

        $query = Payment::query()
            ->with(['patient', 'creator', 'appointment']);

        if ($this->search) {
            $query->whereHas('patient', function ($q) {
                $q->where('first_name', 'ilike', "%{$this->search}%")
                  ->orWhere('last_name', 'ilike', "%{$this->search}%")
                  ->orWhere('identification_number', 'like', "%{$this->search}%");
            })->orWhere('payment_number', 'like', "%{$this->search}%");
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterMethod) {
            $query->where('payment_method', $this->filterMethod);
        }

        if ($this->filterDateFrom) {
            $query->where('payment_date', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->where('payment_date', '<=', $this->filterDateTo);
        }

        $query->orderBy('payment_date', 'desc')->orderBy('id', 'desc');

        $payments = $query->paginate(20);

        $totals = $this->calculateTotals();

        return view('livewire.payments.index', [
            'payments' => $payments,
            'totals' => $totals,
            'paymentMethods' => PaymentMethod::options(),
            'paymentStatuses' => PaymentStatus::options(),
        ]);
    }

    protected function calculateTotals(): array
    {
        $query = Payment::query();

        if ($this->filterDateFrom) {
            $query->where('payment_date', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->where('payment_date', '<=', $this->filterDateTo);
        }

        return [
            'total_amount' => $query->where('status', PaymentStatus::COMPLETED)->sum('amount'),
            'total_count' => $query->where('status', PaymentStatus::COMPLETED)->count(),
            'pending_amount' => $query->where('status', PaymentStatus::PENDING)->sum('amount'),
            'pending_count' => $query->where('status', PaymentStatus::PENDING)->count(),
        ];
    }
}