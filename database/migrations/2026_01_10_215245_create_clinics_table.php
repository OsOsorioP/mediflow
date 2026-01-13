<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clinics', function (Blueprint $table) {
            $table->id();

            // Información básica de la clinica
            $table->string('name');
            $table->string('slug')->unique();

            // Datos de contacto
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();

            // Configuración
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();

            // Límites de plan
            $table->integer('max_users')->default(3);
            $table->integer('max_patients')->default(100);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinics');
    }
};
