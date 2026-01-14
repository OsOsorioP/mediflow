<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Sobrescribir el método can para verificar si el usuario está activo.
 * 
 * @param string|iterable $abilities
 * @param array|mixed $arguments
 * @return bool
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, MultiTenant;

    /**
     * Atributos asignables masivamente
     */
    protected $fillable = [
        'clinic_id',
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_active',
    ];

    /**
     * Atributos ocultos en serialización (JSON)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting de atributos
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class, // Automáticamente convierte string a Enum
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relación: Un usuario pertenece a una clínica
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Verifica si el usuario es administrador
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    /**
     * Verifica si el usuario es asistente
     */
    public function isAssistant(): bool
    {
        return $this->role === UserRole::ASSISTANT;
    }

    /**
     * Verifica si el usuario puede realizar una acción específica
     * (Esto se complementará con Policies en pasos siguientes)
     */
    public function can($abilities, $arguments = []): bool
    {
        // Si el usuario no está activo, denegar cualquier permiso automáticamente
        if (! $this->is_active) {
            return false;
        }

        return parent::can($abilities, $arguments);
    }

    /**
     * Scope: Filtrar solo usuarios activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filtrar por rol
     * Uso: User::role(UserRole::ADMIN)->get()
     */
    public function scopeRole($query, UserRole $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Accessor: Nombre completo con rol
     * Uso: {{ $user->full_name_with_role }}
     */
    public function getFullNameWithRoleAttribute(): string
    {
        return "{$this->name} ({$this->role->label()})";
    }
}
