<?php

namespace Database\Factories;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clinic_id' => Clinic::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'identification_type' => 'CC',
            'identification_number' => fake()->unique()->numerify('##########'),
            'date_of_birth' => fake()->date('Y-m-d', '-18 years'),
            'gender' => fake()->randomElement(['M', 'F']),
            'blood_type' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'O+', 'O-']),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
        ];
    }
}