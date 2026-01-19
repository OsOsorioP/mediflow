<?php

declare(strict_types=1);

namespace App\Enums;

enum MedicalRecordType: string
{
    case CONSULTATION = 'consultation';
    case DIAGNOSIS = 'diagnosis';
    case PRESCRIPTION = 'prescription';
    case LAB_RESULT = 'lab_result';
    case EVOLUTION_NOTE = 'evolution_note';
    case PROCEDURE = 'procedure';

    public function label(): string
    {
        return match($this) {
            self::CONSULTATION => 'Consulta General',
            self::DIAGNOSIS => 'Diagnóstico',
            self::PRESCRIPTION => 'Receta Médica',
            self::LAB_RESULT => 'Resultado de Laboratorio',
            self::EVOLUTION_NOTE => 'Nota de Evolución',
            self::PROCEDURE => 'Procedimiento',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::CONSULTATION => 'clipboard-document-list',
            self::DIAGNOSIS => 'shield-check',
            self::PRESCRIPTION => 'document-text',
            self::LAB_RESULT => 'beaker',
            self::EVOLUTION_NOTE => 'pencil-square',
            self::PROCEDURE => 'wrench-screwdriver',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::CONSULTATION => 'blue',
            self::DIAGNOSIS => 'red',
            self::PRESCRIPTION => 'green',
            self::LAB_RESULT => 'purple',
            self::EVOLUTION_NOTE => 'yellow',
            self::PROCEDURE => 'orange',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
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