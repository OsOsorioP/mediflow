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

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function getEndTimeAttribute(): Carbon
    {
        return $this->scheduled_at->copy()->addMinutes($this->duration_minutes);
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->scheduled_at->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY');
    }

    public function getFormattedTimeAttribute(): string
    {
        return $this->scheduled_at->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', AppointmentStatus::activeStatuses());
    }

    public function scopeStatus($query, AppointmentStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('scheduled_at', '<', now());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_at', today());
    }

    public function scopeBetweenDates($query, Carbon $start, Carbon $end)
    {
        return $query->whereBetween('scheduled_at', [$start, $end]);
    }

    public function scopeForDoctor($query, int $doctorId)
    {
        return $query->where('user_id', $doctorId);
    }

    public function isPast(): bool
    {
        return $this->scheduled_at->isPast();
    }

    public function isToday(): bool
    {
        return $this->scheduled_at->isToday();
    }

    public function isWithinHours(int $hours): bool
    {
        return $this->scheduled_at->isBetween(now(), now()->addHours($hours));
    }

    public function canBeModified(): bool
    {
        return $this->status->canBeModified() && !$this->isPast();
    }

    public function canBeCancelled(): bool
    {
        return $this->status->canBeCancelled() && !$this->isPast();
    }

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

    public function complete(): bool
    {
        if (!$this->status->isActive()) {
            return false;
        }

        return (bool) $this->update(['status' => AppointmentStatus::COMPLETED]);
    }

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

    public function markAsNoShow(): bool
    {
        if (!$this->status->isActive() || !$this->isPast()) {
            return false;
        }

        return (bool) $this->update(['status' => AppointmentStatus::NO_SHOW]);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function isPaid(): bool
    {
        return $this->payment()->where('status', PaymentStatus::COMPLETED)->exists();
    }
}
