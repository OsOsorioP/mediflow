<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            $model->auditAction('created', null, $model->getAuditableAttributes());
        });

        static::updated(function (Model $model) {
            $model->auditAction('updated', $model->getOriginal(), $model->getChanges());
        });
        static::deleted(function (Model $model) {
            $action = $model->isForceDeleting() ? 'force_deleted' : 'deleted';
            $model->auditAction($action, $model->getAuditableAttributes(), null);
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function (Model $model) {
                $model->auditAction('restored', null, $model->getAuditableAttributes());
            });
        }
    }

    public function auditAction(string $action, ?array $oldValues = null, ?array $newValues = null): void
    {
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

    public function auditView(): void
    {
        $this->auditAction('viewed');
    }

    protected function getAuditableAttributes(): array
    {
        $excluded = ['created_at', 'updated_at', 'deleted_at'];
        
        return collect($this->getAttributes())
            ->except($excluded)
            ->toArray();
    }

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

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}