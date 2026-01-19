<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clinic extends Model
{
    use HasFactory, SoftDeletes;

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

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array', 
        'max_users' => 'integer',
        'max_patients' => 'integer',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function canAddUser(): bool
    {
        return $this->users()->count() < $this->max_users;
    }

    public function canAddPatient(): bool
    {
        return $this->patients()->count() < $this->max_patients;
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    public function setSetting(string $key, mixed $value): void
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->settings = $settings;
        $this->save();
    }

    public function workingHours(): HasMany
    {
        return $this->hasMany(WorkingHours::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
