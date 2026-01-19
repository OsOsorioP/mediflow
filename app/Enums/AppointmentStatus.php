<?php

declare(strict_types=1);

namespace App\Enums;

enum AppointmentStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no_show';

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

    public function isActive(): bool
    {
        return $this === self::PENDING || $this === self::CONFIRMED;
    }

    public function canBeModified(): bool
    {
        return $this === self::PENDING || $this === self::CONFIRMED;
    }

    public function canBeCancelled(): bool
    {
        return $this === self::PENDING || $this === self::CONFIRMED;
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $status) {
            $options[$status->value] = $status->label();
        }
        return $options;
    }

    public static function activeStatuses(): array
    {
        return [self::PENDING->value, self::CONFIRMED->value];
    }
}