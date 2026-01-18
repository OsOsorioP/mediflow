<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Traits\Auditable;
use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes, MultiTenant, Auditable;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'appointment_id',
        'medical_record_id',
        'created_by',
        'payment_number',
        'amount',
        'currency',
        'payment_method',
        'status',
        'concept',
        'description',
        'notes',
        'reference_number',
        'payment_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_method' => PaymentMethod::class,
        'status' => PaymentStatus::class,
        'payment_date' => 'date',
    ];

    /**
     * Boot del modelo
     */
    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            // Generar número de pago automáticamente
            if (!$payment->payment_number) {
                $payment->payment_number = static::generatePaymentNumber($payment->clinic_id);
            }
        });
    }

    /**
     * Relación: Clínica
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Relación: Paciente
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relación: Cita (opcional)
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Relación: Registro médico (opcional)
     */
    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    /**
     * Relación: Usuario que creó el pago
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: Pagos completados
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', PaymentStatus::COMPLETED);
    }

    /**
     * Scope: Pagos pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', PaymentStatus::PENDING);
    }

    /**
     * Scope: Por rango de fechas
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Scope: Por método de pago
     */
    public function scopeByMethod($query, PaymentMethod $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Accessor: Monto formateado
     */
    public function getFormattedAmountAttribute(): string
    {
        $symbols = [
            'USD' => '$',
            'COP' => '$',
            'EUR' => '€',
        ];

        $symbol = $symbols[$this->currency] ?? $this->currency;

        return $symbol . number_format((float) $this->amount, 2);
    }

    /**
     * Generar número de pago único
     */
    protected static function generatePaymentNumber(int $clinicId): string
    {
        $year = now()->year;
        $lastPayment = static::where('clinic_id', $clinicId)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastPayment ? (int) substr($lastPayment->payment_number, -5) + 1 : 1;

        return sprintf('PAY-%d-%05d', $year, $sequence);
    }

    /**
     * Verificar si el pago se puede cancelar
     */
    public function canBeCancelled(): bool
    {
        return $this->status === PaymentStatus::COMPLETED || $this->status === PaymentStatus::PENDING;
    }

    /**
     * Cancelar pago
     */
    public function cancel(string|null $reason = null): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        return $this->update([
            'status' => PaymentStatus::CANCELLED,
            'notes' => ($this->notes ?? '') . "\n[Cancelado: " . ($reason ?? 'Sin motivo') . "]",
        ]);
    }

    /**
     * Reembolsar pago
     */
    public function refund(string|null $reason = null): bool
    {
        if ($this->status !== PaymentStatus::COMPLETED) {
            return false;
        }

        return $this->update([
            'status' => PaymentStatus::REFUNDED,
            'notes' => ($this->notes ?? '') . "\n[Reembolsado: " . ($reason ?? 'Sin motivo') . "]",
        ]);
    }
}
