<?php

use App\Livewire\Appointments\Index as AppointmentsIndex;
use App\Livewire\Patients\Index as PatientsIndex;
use App\Livewire\Patients\Show as PatientsShow;
use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Actions\Reports\GeneratePrescriptionPdfAction;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'tenant', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardIndex::class)->name('dashboard');

    Route::view('profile', 'profile')->name('profile');

    // Rutas de Pacientes
    Route::get('/patients', PatientsIndex::class)->name('patients.index');

    Route::get('/patients/create', \App\Livewire\Patients\Create::class)->name('patients.create');

    Route::get('/patients/{patient}', PatientsShow::class)->name('patients.show');

    Route::get('/patients/{patient}/edit', \App\Livewire\Patients\Edit::class)->name('patients.edit');

    Route::get('/patients/{patient}/medical-records/create', \App\Livewire\MedicalRecords\Create::class)->name('medical-records.create');
    Route::get('/patients/{patient}/medical-records/{record}', \App\Livewire\MedicalRecords\ViewRecord::class)->name('medical-records.view');

    // Rutas de citas
    Route::get('/appointments', AppointmentsIndex::class)->name('appointments.index');
    Route::get('/appointments/create', \App\Livewire\Appointments\Create::class)->name('appointments.create');

    // Descargar PDF de receta
    Route::get('/medical-records/{record}/prescription/download', function (MedicalRecord $record) {
        Gate::authorize('view', $record);

        $action = new GeneratePrescriptionPdfAction();
        return $action->download($record);
    })->name('medical-records.prescription.download');

    // Ver PDF en el navegador
    Route::get('/medical-records/{record}/prescription/stream', function (MedicalRecord $record) {
        Gate::authorize('view', $record);
        
        $action = new GeneratePrescriptionPdfAction();
        return $action->stream($record);
    })->name('medical-records.prescription.stream');
});

require __DIR__.'/auth.php';