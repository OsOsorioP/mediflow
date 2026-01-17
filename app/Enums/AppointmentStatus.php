<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Define los estados posibles de una cita médica.
 */
enum AppointmentStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no_show';

    /**
     * Label legible
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendiente',
            self::CONFIRMED => 'Confirmada',
            self::COMPLETED => 'Completada',
            self::CANCELLED => 'Cancelada',
            self::NO_SHOW => 'No Asistió',
        };
    }

    /**
     * Color para badges en UI
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::CONFIRMED => 'blue',
            self::COMPLETED => 'green',
            self::CANCELLED => 'red',
            self::NO_SHOW => 'gray',
        };
    }

    /**
     * Icono
     */
    public function icon(): string
    {
        return match($this) {
            self::PENDING => 'clock',
            self::CONFIRMED => 'check-circle',
            self::COMPLETED => 'check-badge',
            self::CANCELLED => 'x-circle',
            self::NO_SHOW => 'exclamation-triangle',
        };
    }

    /**
     * Verifica si la cita está activa (no cancelada ni completada)
     */
    public function isActive(): bool
    {
        return $this === self::PENDING || $this === self::CONFIRMED;
    }

    /**
     * Verifica si se puede modificar la cita
     */
    public function canBeModified(): bool
    {
        return $this === self::PENDING || $this === self::CONFIRMED;
    }

    /**
     * Verifica si se puede cancelar
     */
    public function canBeCancelled(): bool
    {
        return $this === self::PENDING || $this === self::CONFIRMED;
    }

    /**
     * Valores para selects
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $status) {
            $options[$status->value] = $status->label();
        }
        return $options;
    }

    /**
     * Solo estados activos
     */
    public static function activeStatuses(): array
    {
        return [self::PENDING->value, self::CONFIRMED->value];
    }
}