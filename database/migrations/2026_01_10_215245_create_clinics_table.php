<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinics', function (Blueprint $table) {
            $table->id();
            
            // Información básica de la clínica
            $table->string('name');
            $table->string('slug')->unique(); // URL amigable: /clinic/dr-perez
            
            // Datos de contacto
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            
            // Configuración
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // Para configuraciones futuras
            
            // Límites de plan (para futuro modelo SaaS)
            $table->integer('max_users')->default(3);
            $table->integer('max_patients')->default(100);
            
            $table->timestamps();
            $table->softDeletes(); // Nunca eliminar clínicas completamente
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinics');
    }
};