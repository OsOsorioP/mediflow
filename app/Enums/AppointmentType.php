<?php

declare(strict_types=1);

namespace App\Enums;

enum AppointmentType: string
{
    case CONSULTATION = 'consultation';
    case FOLLOW_UP = 'follow_up';
    case PROCEDURE = 'procedure';
    case EMERGENCY = 'emergency';

    public function label(): string
    {
        return match($this) {
            self::CONSULTATION => 'Consulta General',
            self::FOLLOW_UP => 'Control',
            self::PROCEDURE => 'Procedimiento',
            self::EMERGENCY => 'Urgencia',
        };
    }

    public function defaultDuration(): int
    {
        return match($this) {
            self::CONSULTATION => 30,
            self::FOLLOW_UP => 20,
            self::PROCEDURE => 60,
            self::EMERGENCY => 45,
        };
    }

    public function color(): string
    {
        return match($this) {
            self::CONSULTATION => 'blue',
            self::FOLLOW_UP => 'green',
            self::PROCEDURE => 'purple',
            self::EMERGENCY => 'red',
        };
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $type) {
            $options[$type->value] = $type->label();
        }
        return $options;
    }
}