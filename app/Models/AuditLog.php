<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'clinic_id',
        'user_id',
        'auditable_type',
        'auditable_id',
        'action',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (AuditLog $log) {
            $log->created_at = now();
        });
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function scopeForModel($query, string $modelClass)
    {
        return $query->where('auditable_type', $modelClass);
    }

    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function getActionDescriptionAttribute(): string
    {
        $modelName = class_basename($this->auditable_type);
        $userName = $this->user?->name ?? 'Sistema';

        return match($this->action) {
            'created' => "{$userName} creó un registro de {$modelName}",
            'updated' => "{$userName} actualizó un registro de {$modelName}",
            'deleted' => "{$userName} eliminó un registro de {$modelName}",
            'viewed' => "{$userName} visualizó un registro de {$modelName}",
            'restored' => "{$userName} restauró un registro de {$modelName}",
            default => "{$userName} realizó '{$this->action}' en {$modelName}",
        };
    }

    public function getChangesAttribute(): array
    {
        if ($this->action !== 'updated' || !$this->new_values) {
            return [];
        }

        $changes = [];
        foreach ($this->new_values as $key => $newValue) {
            $oldValue = $this->old_values[$key] ?? null;
            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }
}