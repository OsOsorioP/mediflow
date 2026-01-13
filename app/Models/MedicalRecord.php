<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MedicalRecordType;
use App\Traits\Auditable;
use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class MedicalRecord extends Model
{
    use HasFactory, SoftDeletes, MultiTenant, Auditable;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'created_by',
        'record_type',
        'chief_complaint',
        'symptoms',
        'diagnosis',
        'treatment_plan',
        'prescriptions',
        'clinical_notes',
        'consultation_date',
        'weight',
        'height',
        'blood_pressure',
        'temperature',
        'heart_rate',
        'attachments',
    ];

    protected $casts = [
        'record_type' => MedicalRecordType::class,
        'consultation_date' => 'date',
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
        'temperature' => 'decimal:2',
        'heart_rate' => 'integer',
        'attachments' => 'array',
    ];

    /**
     * Boot del modelo - Auditar visualizaciones
     */
    protected static function booted(): void
    {
        // Cada vez que se carga un MedicalRecord, registrarlo
        static::retrieved(function (MedicalRecord $record) {
            // Solo auditar si es una petición HTTP real (no en consola/queue)
            if (app()->runningInConsole() === false && request()->isMethod('GET')) {
                $record->auditView();
            }
        });
    }

    /**
     * Relación: Pertenece a un paciente
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relación: Creado por un usuario (médico/asistente)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * ENCRIPTACIÓN: Síntomas
     * Laravel automáticamente encripta/desencripta usando Attribute
     */
    protected function symptoms(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? Crypt::decryptString($value) : null,
            set: fn (?string $value) => $value ? Crypt::encryptString($value) : null,
        );
    }

    /**
     * ENCRIPTACIÓN: Diagnóstico
     */
    protected function diagnosis(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? Crypt::decryptString($value) : null,
            set: fn (?string $value) => $value ? Crypt::encryptString($value) : null,
        );
    }

    /**
     * ENCRIPTACIÓN: Plan de tratamiento
     */
    protected function treatmentPlan(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? Crypt::decryptString($value) : null,
            set: fn (?string $value) => $value ? Crypt::encryptString($value) : null,
        );
    }

    /**
     * ENCRIPTACIÓN: Prescripciones
     */
    protected function prescriptions(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? Crypt::decryptString($value) : null,
            set: fn (?string $value) => $value ? Crypt::encryptString($value) : null,
        );
    }

    /**
     * ENCRIPTACIÓN: Notas clínicas
     */
    protected function clinicalNotes(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? Crypt::decryptString($value) : null,
            set: fn (?string $value) => $value ? Crypt::encryptString($value) : null,
        );
    }

    /**
     * Scope: Filtrar por tipo de registro
     */
    public function scopeType($query, MedicalRecordType $type)
    {
        return $query->where('record_type', $type);
    }

    /**
     * Scope: Registros recientes primero
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('consultation_date', 'desc');
    }

    /**
     * Accessor: IMC (Índice de Masa Corporal) calculado
     */
    public function getBmiAttribute(): ?float
    {
        if (!$this->weight || !$this->height) {
            return null;
        }

        $heightInMeters = $this->height / 100;
        return round($this->weight / ($heightInMeters ** 2), 2);
    }
}