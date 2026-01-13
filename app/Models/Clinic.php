<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property bool $is_active
 *
 * @use HasFactory<\Database\Factories\ClinicFactory>
 */
class Clinic extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Atributos que se pueden asignar masivamente
     */
    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'is_active',
        'settings',
        'max_users',
        'max_patients',
    ];

    /**
     * Atributos que deben ser casteados a tipos nativos
     */
    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array', // JSON se convierte automáticamente a array
        'max_users' => 'integer',
        'max_patients' => 'integer',
    ];

    /**
     * Relación: Una clínica tiene muchos usuarios
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope: Filtrar solo clínicas activas
     * Uso: Clinic::active()->get()
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Verifica si la clínica puede agregar más usuarios
     */
    public function canAddUser(): bool
    {
        return $this->users()->count() < $this->max_users;
    }

    /**
     * Verifica si la clínica puede agregar más pacientes
     * (Implementaremos la relación con Patient en fases posteriores)
     */
    public function canAddPatient(): bool
    {
        // Por ahora retornamos true, lo completaremos en Fase 2
        return true;
    }

    /**
     * Accessor: Obtiene una configuración específica del JSON
     * Uso: $clinic->getSetting('timezone', 'America/Bogota')
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    /**
     * Establece una configuración específica
     */
    public function setSetting(string $key, mixed $value): void
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->settings = $settings;
        $this->save();
    }
}
