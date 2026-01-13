<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $clinic_id
 *
 * @method static static|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder query()
 */
trait MultiTenant
{
    /**
     * Boot del trait - Se ejecuta cuando el Model se inicializa
     */
    protected static function bootMultiTenant(): void
    {
        // Automáticamente asignar clinic_id al crear un registro
        static::creating(function (Model $model) {
            if (! $model->clinic_id && auth()->check()) {
                $model->clinic_id = auth()->user()->clinic_id;
            }
        });

        // Global Scope: Filtrar TODOS los queries por clinic_id automáticamente
        static::addGlobalScope('clinic', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where(
                    $builder->getQuery()->from.'.clinic_id',
                    auth()->user()->clinic_id
                );
            }
        });
    }

    /**
     * Relación: Pertenece a una clínica
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Scope para bypassear el filtro de tenant (usar con MUCHO cuidado)
     * Uso: Patient::withoutGlobalScope('clinic')->get()
     *
     * Solo debe usarse en:
     * - Comandos de consola administrativos
     * - Reportes super-admin cross-clinic
     * - Migraciones de datos
     *
     * @param  Builder<Model>  $query
     */
    public function scopeAllClinics(Builder $query): Builder
    {
        return $query->withoutGlobalScope('clinic');
    }
}
