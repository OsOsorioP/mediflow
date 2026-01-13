<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clínica 1: Consultorio Dr. Pérez
        $clinic1 = Clinic::create([
            'name' => 'Consultorio Dr. Pérez',
            'slug' => 'dr-perez',
            'email' => 'contacto@drperez.com',
            'phone' => '3001234567',
            'address' => 'Calle 123 #45-67, Bogotá',
            'is_active' => true,
            'max_users' => 5,
            'max_patients' => 200,
        ]);

        // Admin de Clínica 1
        User::create([
            'clinic_id' => $clinic1->id,
            'name' => 'Dr. Juan Pérez',
            'email' => 'admin@drperez.com',
            'password' => bcrypt('password'),
            'role' => UserRole::ADMIN,
            'phone' => '3001111111',
            'is_active' => true,
        ]);

        // Asistente de Clínica 1
        User::create([
            'clinic_id' => $clinic1->id,
            'name' => 'María Rodríguez',
            'email' => 'maria@drperez.com',
            'password' => bcrypt('password'),
            'role' => UserRole::ASSISTANT,
            'phone' => '3002222222',
            'is_active' => true,
        ]);

        // Clínica 2: Centro Médico Salud Total (para probar multi-tenancy)
        $clinic2 = Clinic::create([
            'name' => 'Centro Médico Salud Total',
            'slug' => 'salud-total',
            'email' => 'info@saludtotal.com',
            'phone' => '3009876543',
            'address' => 'Avenida 80 #12-34, Medellín',
            'is_active' => true,
            'max_users' => 3,
            'max_patients' => 100,
        ]);

        // Admin de Clínica 2
        User::create([
            'clinic_id' => $clinic2->id,
            'name' => 'Dra. Ana García',
            'email' => 'admin@saludtotal.com',
            'password' => bcrypt('password'),
            'role' => UserRole::ADMIN,
            'phone' => '3003333333',
            'is_active' => true,
        ]);

        // Usuario inactivo para testing
        User::create([
            'clinic_id' => $clinic1->id,
            'name' => 'Usuario Inactivo',
            'email' => 'inactivo@drperez.com',
            'password' => bcrypt('password'),
            'role' => UserRole::ASSISTANT,
            'is_active' => false,
        ]);

        $this->command->info('✅ Seeders ejecutados correctamente');
        $this->command->info('📧 Credenciales de prueba:');
        $this->command->table(
            ['Clínica', 'Email', 'Password', 'Rol'],
            [
                ['Dr. Pérez', 'admin@drperez.com', 'password', 'Admin'],
                ['Dr. Pérez', 'maria@drperez.com', 'password', 'Asistente'],
                ['Salud Total', 'admin@saludtotal.com', 'password', 'Admin'],
            ]
        );
    }
}
