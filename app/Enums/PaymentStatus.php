<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentStatus: string
{
    case COMPLETED = 'completed';
    case PENDING = 'pending';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::COMPLETED => 'Completado',
            self::PENDING => 'Pendiente',
            self::CANCELLED => 'Cancelado',
            self::REFUNDED => 'Reembolsado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::COMPLETED => 'green',
            self::PENDING => 'yellow',
            self::CANCELLED => 'red',
            self::REFUNDED => 'gray',
        };
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $status) {
            $options[$status->value] = $status->label();
        }
        return $options;
    }
}