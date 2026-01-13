<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use App\Traits\MultiTenant;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property int $clinic_id
 * @property string $name
 * @property string $email
 * @property \App\Enums\UserRole $role
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clinic|null $clinic
 *
 * @use HasFactory<\Database\Factories\UserFactory>
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, MultiTenant, Notifiable;

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
            'role' => \App\Enums\UserRole::class, // Automáticamente convierte string a Enum
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
     * public function can(string $ability, mixed $arguments = []): bool
     * {
     *   // Si no está activo, no puede hacer nada
     *   if (!$this->is_active) {
     *       return false;
     *   }
     *
     *    return parent::can($ability, $arguments);
     * }
     */

    /**
     * Scope: Filtrar solo usuarios activos
     *
     * @param  Builder<User>  $query
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filtrar por rol
     * Uso: User::role(UserRole::ADMIN)->get()
     *
     * @param  Builder<User>  $query
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
