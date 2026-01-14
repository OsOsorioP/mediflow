<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case ASSISTANT = 'assistant';

    /**
     * Obtiene un label legible para humanos
     */
    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrador (Médico)',
            self::ASSISTANT => 'Asistente (Secretaria)',
        };
    }

    /**
     * Verifica si el rol tiene privilegios administrativos
     */
    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Obtiene todos los valores posibles como array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Obtiene un array para usar en selects de formularios
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $role) {
            $options[$role->value] = $role->label();
        }
        return $options;
    }
}