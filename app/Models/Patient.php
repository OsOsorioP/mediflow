<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes, MultiTenant, Auditable;

    protected $fillable = [
        'clinic_id',
        'first_name',
        'last_name',
        'identification_type',
        'identification_number',
        'date_of_birth',
        'gender',
        'blood_type',
        'email',
        'phone',
        'mobile_phone',
        'address',
        'city',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Relación: Un paciente tiene muchos registros médicos
     */
    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    /**
     * Accessor: Nombre completo del paciente
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Accessor: Edad calculada a partir de fecha de nacimiento
     */
    public function getAgeAttribute(): int
    {
        return $this->date_of_birth->age;
    }

    /**
     * Accessor: Identificación completa (tipo + número)
     */
    public function getFullIdentificationAttribute(): string
    {
        return "{$this->identification_type} {$this->identification_number}";
    }

    /**
     * Scope: Pacientes activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Buscar pacientes por nombre o documento
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'ilike', "%{$search}%")
                ->orWhere('last_name', 'ilike', "%{$search}%")
                ->orWhere('identification_number', 'like', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    /**
     * Scope: Filtrar por género
     */
    public function scopeGender($query, string $gender)
    {
        return $query->where('gender', $gender);
    }

    /**
     * Obtiene el último registro médico
     */
    public function getLatestMedicalRecord(): ?MedicalRecord
    {
        return $this->medicalRecords()
            ->latest('consultation_date')
            ->first();
    }

    /**
     * Cuenta total de consultas del paciente
     */
    public function getTotalConsultationsAttribute(): int
    {
        return $this->medicalRecords()->count();
    }

    /**
     * Relación: Un paciente tiene muchas citas
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
