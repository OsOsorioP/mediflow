<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case ASSISTANT = 'assistant';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrador (Médico)',
            self::ASSISTANT => 'Asistente (Secretaria)',
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $role) {
            $options[$role->value] = $role->label();
        }
        return $options;
    }
}