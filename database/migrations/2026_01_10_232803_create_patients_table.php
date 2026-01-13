<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            
            // Multi-tenancy
            $table->foreignId('clinic_id')
                ->constrained()
                ->cascadeOnDelete();
            
            // Información personal básica (no sensible)
            $table->string('first_name');
            $table->string('last_name');
            $table->string('identification_type')->default('CC'); // CC, TI, CE, Pasaporte
            $table->string('identification_number')->unique();
            $table->date('date_of_birth');
            $table->enum('gender', ['M', 'F', 'O'])->nullable(); // Masculino, Femenino, Otro
            $table->string('blood_type')->nullable(); // A+, O-, etc.
            
            // Contacto
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('mobile_phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            
            // Contacto de emergencia
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            
            // Estado
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable(); // Notas administrativas (no médicas)
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Índices para mejorar rendimiento
            $table->index(['clinic_id', 'is_active']);
            $table->index('identification_number');
            $table->index(['first_name', 'last_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};