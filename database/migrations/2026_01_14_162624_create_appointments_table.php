<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            
            // Multi-tenancy
            $table->foreignId('clinic_id')
                ->constrained()
                ->cascadeOnDelete();
            
            // Relaciones principales
            $table->foreignId('patient_id')
                ->constrained()
                ->restrictOnDelete(); // No permitir eliminar paciente con citas
            
            $table->foreignId('user_id') // Médico/profesional asignado
                ->constrained()
                ->restrictOnDelete();
            
            // Información de la cita
            $table->dateTime('scheduled_at'); // Fecha y hora de inicio
            $table->integer('duration_minutes')->default(30); // Duración en minutos
            
            // Estado de la cita
            $table->string('status')->default('pending'); 
            // pending, confirmed, completed, cancelled, no_show
            
            // Información adicional
            $table->string('appointment_type')->nullable(); // consultation, follow_up, procedure
            $table->text('reason')->nullable(); // Motivo de la cita
            $table->text('notes')->nullable(); // Notas administrativas
            $table->text('cancellation_reason')->nullable(); // Si se cancela, por qué
            
            // Usuario que creó la cita
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Índices críticos para rendimiento
            $table->index(['clinic_id', 'scheduled_at', 'status']);
            $table->index(['patient_id', 'scheduled_at']);
            $table->index(['user_id', 'scheduled_at']);
            $table->index('status');
            
            // Índice único para prevenir double-booking a nivel de BD
            // No dos citas activas para el mismo médico a la misma hora
            $table->unique(
                ['user_id', 'scheduled_at', 'deleted_at'],
                'unique_active_appointment_per_doctor'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};