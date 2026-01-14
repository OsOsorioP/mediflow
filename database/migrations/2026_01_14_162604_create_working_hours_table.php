<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('working_hours', function (Blueprint $table) {
            $table->id();
            
            // Multi-tenancy
            $table->foreignId('clinic_id')
                ->constrained()
                ->cascadeOnDelete();
            
            // Día de la semana (0 = Domingo, 1 = Lunes, ..., 6 = Sábado)
            $table->tinyInteger('day_of_week'); // 0-6
            
            // Horarios
            $table->time('start_time'); // Ej: 08:00
            $table->time('end_time');   // Ej: 17:00
            
            // Control
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Índices
            $table->index(['clinic_id', 'day_of_week', 'is_active']);
            
            // Constraint: No permitir horarios superpuestos para el mismo día en la misma clínica
            $table->unique(['clinic_id', 'day_of_week', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('working_hours');
    }
};