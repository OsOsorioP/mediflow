<?php

use App\Actions\Appointments\ScheduleAppointmentAction;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\User;
use App\Models\WorkingHours;
use App\Enums\UserRole;
use Carbon\Carbon;

beforeEach(function () {
    $this->clinic = Clinic::factory()->create();
    
    $this->doctor = User::factory()->create([
        'clinic_id' => $this->clinic->id,
        'role' => UserRole::ADMIN,
    ]);
    
    $this->patient1 = Patient::factory()->create(['clinic_id' => $this->clinic->id]);
    $this->patient2 = Patient::factory()->create(['clinic_id' => $this->clinic->id]);
    
    // Crear horarios de atención (Lunes a Viernes)
    for ($day = 1; $day <= 5; $day++) {
        WorkingHours::create([
            'clinic_id' => $this->clinic->id,
            'day_of_week' => $day,
            'start_time' => '08:00',
            'end_time' => '17:00',
            'is_active' => true,
        ]);
    }
    
    $this->actingAs($this->doctor);
});

test('can create an appointment successfully', function () {
    $action = new ScheduleAppointmentAction();
    
    $scheduledAt = Carbon::parse('next monday 10:00');
    
    $appointment = $action->execute([
        'patient_id' => $this->patient1->id,
        'user_id' => $this->doctor->id,
        'scheduled_at' => $scheduledAt,
        'duration_minutes' => 30,
        'appointment_type' => 'consultation',
    ]);
    
    expect($appointment)->not->toBeNull()
        ->and($appointment->patient_id)->toBe($this->patient1->id)
        ->and($appointment->scheduled_at->format('Y-m-d H:i'))->toBe($scheduledAt->format('Y-m-d H:i'));
});

test('prevents double booking for same doctor', function () {
    $action = new ScheduleAppointmentAction();
    $scheduledAt = Carbon::parse('next monday 10:00');
    
    // Primera cita
    $action->execute([
        'patient_id' => $this->patient1->id,
        'user_id' => $this->doctor->id,
        'scheduled_at' => $scheduledAt,
        'duration_minutes' => 30,
    ]);
    
    // Intentar crear otra cita al mismo tiempo
    $action->execute([
        'patient_id' => $this->patient2->id,
        'user_id' => $this->doctor->id,
        'scheduled_at' => $scheduledAt, // Misma hora
        'duration_minutes' => 30,
    ]);
})->throws(\Illuminate\Validation\ValidationException::class);

test('prevents appointments outside working hours', function () {
    $action = new ScheduleAppointmentAction();
    
    // Intentar agendar a las 6 AM (fuera de horario)
    $action->execute([
        'patient_id' => $this->patient1->id,
        'user_id' => $this->doctor->id,
        'scheduled_at' => Carbon::parse('next monday 06:00'),
        'duration_minutes' => 30,
    ]);
})->throws(\Illuminate\Validation\ValidationException::class);

test('prevents appointments in the past', function () {
    $action = new ScheduleAppointmentAction();
    
    $action->execute([
        'patient_id' => $this->patient1->id,
        'user_id' => $this->doctor->id,
        'scheduled_at' => Carbon::yesterday(),
        'duration_minutes' => 30,
    ]);
})->throws(\Illuminate\Validation\ValidationException::class);

test('detects overlapping appointments correctly', function () {
    $action = new ScheduleAppointmentAction();
    $baseTime = Carbon::parse('next monday 10:00');
    
    // Crear cita de 10:00 a 10:30
    $action->execute([
        'patient_id' => $this->patient1->id,
        'user_id' => $this->doctor->id,
        'scheduled_at' => $baseTime,
        'duration_minutes' => 30,
    ]);
    
    // Intentar crear cita de 10:15 a 10:45 (se superpone)
    $action->execute([
        'patient_id' => $this->patient2->id,
        'user_id' => $this->doctor->id,
        'scheduled_at' => $baseTime->copy()->addMinutes(15),
        'duration_minutes' => 30,
    ]);
})->throws(\Illuminate\Validation\ValidationException::class);