<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    // No usar timestamps automáticos, solo created_at
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

    /**
     * Boot del modelo
     */
    protected static function booted(): void
    {
        // Establecer created_at automáticamente
        static::creating(function (AuditLog $log) {
            $log->created_at = now();
        });
    }

    /**
     * Relación polimórfica: El modelo auditado
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relación: Usuario que realizó la acción
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Clínica
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Scope: Filtrar por tipo de modelo auditado
     */
    public function scopeForModel($query, string $modelClass)
    {
        return $query->where('auditable_type', $modelClass);
    }

    /**
     * Scope: Filtrar por acción específica
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: Logs recientes primero
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Accessor: Descripción legible de la acción
     */
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

    /**
     * Obtiene los cambios realizados (para action = updated)
     */
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