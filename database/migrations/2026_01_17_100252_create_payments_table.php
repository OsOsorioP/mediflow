<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            // Multi-tenancy
            $table->foreignId('clinic_id')
                ->constrained()
                ->cascadeOnDelete();
            
            // Relaciones
            $table->foreignId('patient_id')
                ->constrained()
                ->restrictOnDelete();
            
            $table->foreignId('appointment_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            
            $table->foreignId('medical_record_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            
            // Usuario que registró el pago
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();
            
            // Información del pago
            $table->string('payment_number')->unique(); // PAY-2024-00001
            $table->decimal('amount', 10, 2); // Monto
            $table->string('currency', 3)->default('USD'); // USD, COP, etc
            $table->string('payment_method'); // cash, card, transfer, insurance
            $table->string('status')->default('completed'); // completed, pending, cancelled, refunded
            
            // Detalles
            $table->string('concept'); // "Consulta General", "Procedimiento", etc
            $table->text('description')->nullable();
            $table->text('notes')->nullable(); // Notas internas
            
            // Información adicional
            $table->string('reference_number')->nullable(); // Número de transacción/cheque
            $table->date('payment_date');
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['clinic_id', 'payment_date']);
            $table->index(['patient_id', 'payment_date']);
            $table->index('status');
            $table->index('payment_method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};