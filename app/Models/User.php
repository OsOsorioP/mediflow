<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, MultiTenant;

    protected $fillable = [
        'clinic_id',
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isAssistant(): bool
    {
        return $this->role === UserRole::ASSISTANT;
    }

    public function can($abilities, $arguments = []): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return parent::can($abilities, $arguments);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRole($query, UserRole $role)
    {
        return $query->where('role', $role);
    }

    public function getFullNameWithRoleAttribute(): string
    {
        return "{$this->name} ({$this->role->label()})";
    }
}
