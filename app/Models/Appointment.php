<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AppointmentStatus;
use App\Enums\AppointmentType;
use App\Enums\PaymentStatus;
use App\Traits\Auditable;
use App\Traits\MultiTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes, MultiTenant, Auditable;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'user_id',
        'scheduled_at',
        'duration_minutes',
        'status',
        'appointment_type',
        'reason',
        'notes',
        'cancellation_reason',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'duration_minutes' => 'integer',
        'status' => AppointmentStatus::class,
        'appointment_type' => AppointmentType::class,
    ];

    /**
     * Relación: Paciente
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relación: Profesional/Médico asignado
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación: Usuario que creó la cita
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación: Clínica
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Accessor: Hora de finalización
     */
    public function getEndTimeAttribute(): Carbon
    {
        return $this->scheduled_at->copy()->addMinutes($this->duration_minutes);
    }

    /**
     * Accessor: Fecha formateada
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->scheduled_at->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY');
    }

    /**
     * Accessor: Hora formateada
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->scheduled_at->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    /**
     * Scope: Citas activas (pendientes o confirmadas)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', AppointmentStatus::activeStatuses());
    }

    /**
     * Scope: Citas por estado
     */
    public function scopeStatus($query, AppointmentStatus $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Citas futuras
     */
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now());
    }

    /**
     * Scope: Citas pasadas
     */
    public function scopePast($query)
    {
        return $query->where('scheduled_at', '<', now());
    }

    /**
     * Scope: Citas de hoy
     */
    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_at', today());
    }

    /**
     * Scope: Citas en un rango de fechas
     */
    public function scopeBetweenDates($query, Carbon $start, Carbon $end)
    {
        return $query->whereBetween('scheduled_at', [$start, $end]);
    }

    /**
     * Scope: Citas de un médico específico
     */
    public function scopeForDoctor($query, int $doctorId)
    {
        return $query->where('user_id', $doctorId);
    }

    /**
     * Verifica si la cita está en el pasado
     */
    public function isPast(): bool
    {
        return $this->scheduled_at->isPast();
    }

    /**
     * Verifica si la cita es hoy
     */
    public function isToday(): bool
    {
        return $this->scheduled_at->isToday();
    }

    /**
     * Verifica si la cita está dentro de las próximas X horas
     */
    public function isWithinHours(int $hours): bool
    {
        return $this->scheduled_at->isBetween(now(), now()->addHours($hours));
    }

    /**
     * Verifica si se puede modificar la cita
     */
    public function canBeModified(): bool
    {
        return $this->status->canBeModified() && !$this->isPast();
    }

    /**
     * Verifica si se puede cancelar
     */
    public function canBeCancelled(): bool
    {
        return $this->status->canBeCancelled() && !$this->isPast();
    }

    /**
     * Confirmar la cita
     */
    public function confirm(): bool
    {
        if ($this->status !== AppointmentStatus::PENDING) {
            return false;
        }

        $result = (bool) $this->update(['status' => AppointmentStatus::CONFIRMED]);

        if ($result) {
            event(new \App\Events\AppointmentConfirmed($this));
        }

        return $result;
    }

    /**
     * Completar la cita
     */
    public function complete(): bool
    {
        if (!$this->status->isActive()) {
            return false;
        }

        return (bool) $this->update(['status' => AppointmentStatus::COMPLETED]);
    }

    /**
     * Cancelar la cita
     */
    public function cancel(string|null $reason = null): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        return (bool) $this->update([
            'status' => AppointmentStatus::CANCELLED,
            'cancellation_reason' => $reason,
        ]);
    }

    /**
     * Marcar como no asistió
     */
    public function markAsNoShow(): bool
    {
        if (!$this->status->isActive() || !$this->isPast()) {
            return false;
        }

        return (bool) $this->update(['status' => AppointmentStatus::NO_SHOW]);
    }

    /**
     * Relación: Una cita puede tener un pago
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Verificar si la cita está pagada
     */
    public function isPaid(): bool
    {
        return $this->payment()->where('status', PaymentStatus::COMPLETED)->exists();
    }
}
