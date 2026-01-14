<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Trait Auditable
 * 
 * Registra automáticamente todas las acciones (created, updated, deleted, viewed)
 * en la tabla audit_logs para cumplir con regulaciones médicas.
 */
trait Auditable
{
    /**
     * Boot del trait
     */
    protected static function bootAuditable(): void
    {
        // Auditar cuando se crea un registro
        static::created(function (Model $model) {
            $model->auditAction('created', null, $model->getAuditableAttributes());
        });

        // Auditar cuando se actualiza un registro
        static::updated(function (Model $model) {
            $model->auditAction('updated', $model->getOriginal(), $model->getChanges());
        });

        // Auditar cuando se elimina un registro (soft delete)
        static::deleted(function (Model $model) {
            $action = $model->isForceDeleting() ? 'force_deleted' : 'deleted';
            $model->auditAction($action, $model->getAuditableAttributes(), null);
        });

        // Auditar cuando se restaura un registro
        if (method_exists(static::class, 'restored')) {
            static::restored(function (Model $model) {
                $model->auditAction('restored', null, $model->getAuditableAttributes());
            });
        }
    }

    /**
     * Registra una acción de auditoría
     */
    public function auditAction(string $action, ?array $oldValues = null, ?array $newValues = null): void
    {
        // Obtener clinic_id del modelo o del usuario autenticado
        $clinicId = $this->clinic_id ?? (Auth::check() ? Auth::user()->clinic_id : null);

        AuditLog::create([
            'clinic_id' => $clinicId,
            'user_id' => Auth::id(),
            'auditable_type' => get_class($this),
            'auditable_id' => $this->id,
            'action' => $action,
            'old_values' => $this->filterSensitiveData($oldValues),
            'new_values' => $this->filterSensitiveData($newValues),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
        ]);
    }

    /**
     * Audita explícitamente una vista/acceso al registro
     * Uso: $patient->auditView();
     */
    public function auditView(): void
    {
        $this->auditAction('viewed');
    }

    /**
     * Obtiene los atributos auditables del modelo
     * (Excluye timestamps y claves foráneas por defecto)
     */
    protected function getAuditableAttributes(): array
    {
        $excluded = ['created_at', 'updated_at', 'deleted_at'];
        
        return collect($this->getAttributes())
            ->except($excluded)
            ->toArray();
    }

    /**
     * Filtra datos sensibles antes de guardarlos en audit_logs
     * (No guardar contraseñas, datos encriptados, etc.)
     */
    protected function filterSensitiveData(?array $data): ?array
    {
        if (!$data) {
            return null;
        }

        $sensitiveFields = [
            'password', 
            'remember_token',
            'symptoms', 
            'diagnosis', 
            'treatment_plan', 
            'prescriptions', 
            'clinical_notes'
        ];

        return collect($data)
            ->except($sensitiveFields)
            ->toArray();
    }

    /**
     * Relación con los logs de auditoría
     */
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}