<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            
            // Multi-tenancy
            $table->foreignId('clinic_id')
                ->constrained()
                ->cascadeOnDelete();
            
            // Relación con paciente
            $table->foreignId('patient_id')
                ->constrained()
                ->cascadeOnDelete();
            
            // Usuario que creó el registro (médico/asistente)
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete(); // No permitir eliminar usuario si tiene registros
            
            // Tipo de registro
            $table->string('record_type')->default('consultation'); 
            // consultation, diagnosis, prescription, lab_result, evolution_note
            
            // Contenido médico (ENCRIPTADO)
            $table->text('chief_complaint')->nullable(); // Motivo de consulta
            $table->text('symptoms'); // Síntomas - ENCRIPTADO
            $table->text('diagnosis'); // Diagnóstico - ENCRIPTADO
            $table->text('treatment_plan')->nullable(); // Plan de tratamiento - ENCRIPTADO
            $table->text('prescriptions')->nullable(); // Medicamentos - ENCRIPTADO
            $table->text('clinical_notes'); // Notas clínicas - ENCRIPTADO
            
            // Datos de consulta
            $table->date('consultation_date');
            $table->decimal('weight', 5, 2)->nullable(); // kg
            $table->decimal('height', 5, 2)->nullable(); // cm
            $table->string('blood_pressure')->nullable(); // 120/80
            $table->decimal('temperature', 4, 2)->nullable(); // °C
            $table->integer('heart_rate')->nullable(); // bpm
            
            // Adjuntos (referencias a archivos)
            $table->json('attachments')->nullable(); // URLs de PDFs, imágenes, etc.
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['clinic_id', 'patient_id']);
            $table->index('consultation_date');
            $table->index('record_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};