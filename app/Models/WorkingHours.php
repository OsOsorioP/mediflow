<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\MultiTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkingHours extends Model
{
    use HasFactory, MultiTenant;

    protected $fillable = [
        'clinic_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    /**
     * Relación con clínica
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Scope: Solo horarios activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Por día de la semana
     */
    public function scopeForDay($query, int $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    /**
     * Obtiene el nombre del día
     */
    public function getDayNameAttribute(): string
    {
        return Carbon::parse("Sunday + {$this->day_of_week} days")->locale('es')->dayName;
    }

    /**
     * Verifica si una hora específica está dentro del horario
     */
    public function includesTime(Carbon $time): bool
    {
        $checkTime = $time->format('H:i');
        $start = $this->start_time->format('H:i');
        $end = $this->end_time->format('H:i');

        return $checkTime >= $start && $checkTime < $end;
    }

    /**
     * Obtiene todos los slots disponibles para este horario
     * @param int $slotDuration Duración de cada slot en minutos
     */
    public function getAvailableSlots(int $slotDuration = 30): array
    {
        $slots = [];
        $current = $this->start_time->copy();
        $end = $this->end_time->copy();

        while ($current->lt($end)) {
            $slots[] = $current->format('H:i');
            $current->addMinutes($slotDuration);
        }

        return $slots;
    }

    /**
     * Nombres de días para helpers
     */
    public static function dayNames(): array
    {
        return [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        ];
    }
}