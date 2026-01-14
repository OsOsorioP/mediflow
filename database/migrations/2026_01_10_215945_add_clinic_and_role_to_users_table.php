<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Relación con clínica (cada usuario pertenece a UNA clínica)
            $table->foreignId('clinic_id')
                ->after('id')
                ->constrained()
                ->cascadeOnDelete(); // Si se elimina la clínica, eliminar usuarios
            
            // Rol del usuario dentro de su clínica
            $table->string('role')
                ->after('clinic_id')
                ->default('assistant'); // Por defecto, asistente
            
            // Información adicional
            $table->string('phone')->nullable()->after('email');
            $table->boolean('is_active')->default(true)->after('phone');
            
            // Índices para mejorar rendimiento en consultas frecuentes
            $table->index(['clinic_id', 'role']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
            $table->dropIndex(['clinic_id', 'role']);
            $table->dropIndex(['is_active']);
            $table->dropColumn(['clinic_id', 'role', 'phone', 'is_active']);
        });
    }
};