<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Clinic;
use App\Models\User;
use App\Models\Patient;
use App\Models\MedicalRecord;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Usamos firstOrCreate buscando por el 'slug' único
        $clinic1 = Clinic::firstOrCreate(
            ['slug' => 'dr-perez'], // Condición de búsqueda
            [
                'name' => 'Consultorio Dr. Pérez',
                'email' => 'contacto@drperez.com',
                'phone' => '3001234567',
                'address' => 'Calle 123 #45-67, Bogotá',
                'is_active' => true,
                'max_users' => 5,
                'max_patients' => 200,
            ]
        );

        // 2. Hacemos lo mismo con los usuarios, buscando por 'email'
        User::firstOrCreate(
            ['email' => 'admin@drperez.com'],
            [
                'clinic_id' => $clinic1->id,
                'name' => 'Dr. Juan Pérez',
                'password' => bcrypt('password'),
                'role' => UserRole::ADMIN,
                'phone' => '3001111111',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'maria@drperez.com'],
            [
                'clinic_id' => $clinic1->id,
                'name' => 'María Rodríguez',
                'password' => bcrypt('password'),
                'role' => UserRole::ASSISTANT,
                'phone' => '3002222222',
                'is_active' => true,
            ]
        );

        // --- Clínica 2 ---
        $clinic2 = Clinic::firstOrCreate(
            ['slug' => 'salud-total'],
            [
                'name' => 'Centro Médico Salud Total',
                'email' => 'info@saludtotal.com',
                'phone' => '3009876543',
                'address' => 'Avenida 80 #12-34, Medellín',
                'is_active' => true,
                'max_users' => 3,
                'max_patients' => 100,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@saludtotal.com'],
            [
                'clinic_id' => $clinic2->id,
                'name' => 'Dra. Ana García',
                'password' => bcrypt('password'),
                'role' => UserRole::ADMIN,
                'phone' => '3003333333',
                'is_active' => true,
            ]
        );

        // Usuario inactivo
        User::firstOrCreate(
            ['email' => 'inactivo@drperez.com'],
            [
                'clinic_id' => $clinic1->id,
                'name' => 'Usuario Inactivo',
                'password' => bcrypt('password'),
                'role' => UserRole::ASSISTANT,
                'is_active' => false,
            ]
        );

        // Crear pacientes y registros médicos para Clínica 1
        $patient1 = Patient::create([
            'clinic_id' => $clinic1->id,
            'first_name' => 'Carlos',
            'last_name' => 'Ramírez',
            'identification_type' => 'CC',
            'identification_number' => '1234567890',
            'date_of_birth' => '1985-05-15',
            'gender' => 'M',
            'blood_type' => 'O+',
            'phone' => '3101234567',
            'email' => 'carlos.ramirez@email.com',
            'address' => 'Calle 50 #20-30',
            'city' => 'Bogotá',
        ]);

        MedicalRecord::create([
            'clinic_id' => $clinic1->id,
            'patient_id' => $patient1->id,
            'created_by' => 1, // Dr. Pérez
            'record_type' => 'consultation',
            'chief_complaint' => 'Dolor de cabeza persistente',
            'symptoms' => 'Cefalea intensa desde hace 3 días, fotofobia',
            'diagnosis' => 'Migraña común sin aura',
            'treatment_plan' => 'Analgésicos y reposo',
            'prescriptions' => 'Ibuprofeno 400mg cada 8 horas',
            'clinical_notes' => 'Paciente refiere estrés laboral. Recomendación de técnicas de relajación.',
            'consultation_date' => now()->subDays(5),
            'weight' => 75.5,
            'height' => 175,
            'blood_pressure' => '120/80',
            'temperature' => 36.5,
            'heart_rate' => 72,
        ]);

        $patient2 = Patient::create([
            'clinic_id' => $clinic1->id,
            'first_name' => 'Ana',
            'last_name' => 'López',
            'identification_type' => 'CC',
            'identification_number' => '9876543210',
            'date_of_birth' => '1992-08-20',
            'gender' => 'F',
            'blood_type' => 'A+',
            'phone' => '3209876543',
            'email' => 'ana.lopez@email.com',
            'city' => 'Bogotá',
        ]);

        // Crear horarios de atención para Clínica 1 (Lunes a Viernes, 8AM-5PM)
        for ($day = 1; $day <= 5; $day++) {
            \App\Models\WorkingHours::create([
                'clinic_id' => $clinic1->id,
                'day_of_week' => $day,
                'start_time' => '08:00',
                'end_time' => '17:00',
                'is_active' => true,
            ]);
        }

        // Crear citas de ejemplo
        \App\Models\Appointment::create([
            'clinic_id' => $clinic1->id,
            'patient_id' => $patient1->id,
            'user_id' => 1, // Dr. Pérez
            'scheduled_at' => now()->addDays(1)->setTime(10, 0),
            'duration_minutes' => 30,
            'status' => 'confirmed',
            'appointment_type' => 'consultation',
            'reason' => 'Control general',
            'created_by' => 1,
        ]);

        \App\Models\Appointment::create([
            'clinic_id' => $clinic1->id,
            'patient_id' => $patient2->id,
            'user_id' => 1,
            'scheduled_at' => now()->addDays(1)->setTime(11, 0),
            'duration_minutes' => 30,
            'status' => 'pending',
            'appointment_type' => 'follow_up',
            'reason' => 'Control de tratamiento',
            'created_by' => 2, // María
        ]);

        $this->command->info('✅ Seeders ejecutados correctamente (o datos ya existían)');
    }
}