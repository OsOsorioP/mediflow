<?php

use App\Models\Clinic;
use App\Models\Patient;
use App\Models\User;
use App\Enums\UserRole;

beforeEach(function () {
    // Crear dos clínicas con usuarios
    $this->clinic1 = Clinic::factory()->create(['name' => 'Clinic 1']);
    $this->clinic2 = Clinic::factory()->create(['name' => 'Clinic 2']);
    
    $this->user1 = User::factory()->create([
        'clinic_id' => $this->clinic1->id,
        'role' => UserRole::ADMIN,
    ]);
    
    $this->user2 = User::factory()->create([
        'clinic_id' => $this->clinic2->id,
        'role' => UserRole::ADMIN,
    ]);
    
    // Crear pacientes para cada clínica
    $this->patient1 = Patient::factory()->create(['clinic_id' => $this->clinic1->id]);
    $this->patient2 = Patient::factory()->create(['clinic_id' => $this->clinic2->id]);
});

test('users can only see patients from their clinic', function () {
    $this->actingAs($this->user1);
    
    $patients = Patient::all();
    
    expect($patients)->toHaveCount(1)
        ->and($patients->first()->id)->toBe($this->patient1->id);
});

test('users cannot access patients from other clinics', function () {
    $this->actingAs($this->user1);
    
    $response = $this->get(route('patients.show', $this->patient2));
    
    $response->assertForbidden();
});

test('global scope filters patients by clinic automatically', function () {
    $this->actingAs($this->user1);
    
    // El scope global debe filtrar automáticamente
    $count = Patient::count();
    
    expect($count)->toBe(1);
});

test('patient creation assigns clinic_id automatically', function () {
    $this->actingAs($this->user1);
    
    $patient = Patient::create([
        'first_name' => 'Test',
        'last_name' => 'Patient',
        'identification_type' => 'CC',
        'identification_number' => '123456789',
        'date_of_birth' => '1990-01-01',
        'phone' => '3001234567',
    ]);
    
    expect($patient->clinic_id)->toBe($this->clinic1->id);
});