<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case CARD = 'card';
    case TRANSFER = 'transfer';
    case INSURANCE = 'insurance';
    case CHECK = 'check';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::CASH => 'Efectivo',
            self::CARD => 'Tarjeta',
            self::TRANSFER => 'Transferencia',
            self::INSURANCE => 'Seguro Médico',
            self::CHECK => 'Cheque',
            self::OTHER => 'Otro',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::CASH => '💵',
            self::CARD => '💳',
            self::TRANSFER => '🏦',
            self::INSURANCE => '🏥',
            self::CHECK => '📝',
            self::OTHER => '📄',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::CASH => 'green',
            self::CARD => 'blue',
            self::TRANSFER => 'purple',
            self::INSURANCE => 'yellow',
            self::CHECK => 'gray',
            self::OTHER => 'gray',
        };
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $method) {
            $options[$method->value] = $method->icon() . ' ' . $method->label();
        }
        return $options;
    }
}