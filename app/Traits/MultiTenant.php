<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Clinic;
use App\Models\Scopes\ClinicScope;
use App\Services\TenantManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait MultiTenant
{
    protected static function bootMultiTenant(): void
    {
        static::addGlobalScope(new ClinicScope());

        static::creating(function (Model $model) {
            $manager = app(TenantManager::class);

            if ($manager->hasClinic() && ! $model->clinic_id) {
                $model->clinic_id = $manager->getClinicId();
            }

            if (! $model->clinic_id) {
                throw new \Exception("Error Crítico: No se pudo determinar la clínica para el registro de " . get_class($model));
            }
        });
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function scopeAllClinics(Builder $query): Builder
    {
        return $query->withoutGlobalScope(ClinicScope::class);
    }
}
