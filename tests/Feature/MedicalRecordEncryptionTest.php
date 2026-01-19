<?php

use App\Models\Clinic;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->clinic = Clinic::factory()->create();
    
    $this->doctor = User::factory()->create([
        'clinic_id' => $this->clinic->id,
        'role' => UserRole::ADMIN,
    ]);
    
    $this->patient = Patient::factory()->create(['clinic_id' => $this->clinic->id]);
    
    $this->actingAs($this->doctor);
});

test('sensitive medical data is encrypted in database', function () {
    $sensitiveData = 'Paciente presenta síntomas graves';
    
    $record = MedicalRecord::create([
        'patient_id' => $this->patient->id,
        'created_by' => $this->doctor->id,
        'record_type' => 'consultation',
        'symptoms' => $sensitiveData,
        'diagnosis' => 'Diagnóstico confidencial',
        'clinical_notes' => 'Notas médicas privadas',
        'consultation_date' => today(),
    ]);
    
    $rawRecord = DB::table('medical_records')->find($record->id);
    
    expect($rawRecord->symptoms)->not->toBe($sensitiveData)
        ->and(str_contains($rawRecord->symptoms, $sensitiveData))->toBeFalse();
});

test('encrypted data is automatically decrypted when accessed', function () {
    $originalSymptoms = 'Dolor intenso en el pecho';
    $originalDiagnosis = 'Angina de pecho';
    
    $record = MedicalRecord::create([
        'patient_id' => $this->patient->id,
        'created_by' => $this->doctor->id,
        'record_type' => 'consultation',
        'symptoms' => $originalSymptoms,
        'diagnosis' => $originalDiagnosis,
        'clinical_notes' => 'Requiere atención urgente',
        'consultation_date' => today(),
    ]);
    
    $record->refresh();

    expect($record->symptoms)->toBe($originalSymptoms)
        ->and($record->diagnosis)->toBe($originalDiagnosis);
});

test('null values are handled correctly in encryption', function () {
    $record = MedicalRecord::create([
        'patient_id' => $this->patient->id,
        'created_by' => $this->doctor->id,
        'record_type' => 'consultation',
        'symptoms' => 'Síntomas básicos',
        'diagnosis' => 'Diagnóstico simple',
        'clinical_notes' => 'Notas',
        'consultation_date' => today(),
        'prescriptions' => null,
    ]);
    
    expect($record->prescriptions)->toBeNull();
});