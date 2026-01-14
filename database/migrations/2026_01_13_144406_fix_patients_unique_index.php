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
        Schema::table('patients', function (Blueprint $table) {
        // Eliminamos el índice único viejo que solo miraba el número
        $table->dropUnique(['identification_number']);
        
        // Creamos uno nuevo que sea único PERO por clínica
        $table->unique(['clinic_id', 'identification_number']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
        $table->dropUnique(['clinic_id', 'identification_number']);
        $table->unique(['identification_number']);
    });
    }
};
