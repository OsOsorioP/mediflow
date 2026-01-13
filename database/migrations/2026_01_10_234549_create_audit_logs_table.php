<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            
            // Multi-tenancy
            $table->foreignId('clinic_id')
                ->constrained()
                ->cascadeOnDelete();
            
            // Usuario que realizó la acción
            $table->foreignId('user_id')
                ->nullable() // Puede ser null si fue sistema automático
                ->constrained()
                ->nullOnDelete();
            
            // Qué se auditó
            $table->string('auditable_type'); // App\Models\Patient, App\Models\MedicalRecord
            $table->unsignedBigInteger('auditable_id'); // ID del registro auditado
            
            // Acción realizada
            $table->string('action'); // created, updated, viewed, deleted, restored
            
            // Datos adicionales
            $table->json('old_values')->nullable(); // Estado anterior (para updates)
            $table->json('new_values')->nullable(); // Estado nuevo
            
            // Contexto
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable();
            
            $table->timestamp('created_at'); // Solo created_at, no updated_at
            
            // Índices para consultas frecuentes
            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['clinic_id', 'user_id']);
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};