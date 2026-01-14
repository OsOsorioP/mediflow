<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Services\TenantManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ClinicScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $manager = app(TenantManager::class);

        if ($manager->hasClinic()) {
            $builder->where($model->getTable() . '.clinic_id', $manager->getClinicId());
        }
    }
}