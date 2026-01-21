<?php

use App\Livewire\Patients\Index as PatientsIndex;
use App\Livewire\Patients\Show as PatientsShow;
use App\Livewire\Patients\Create as PatientsCreate;
use App\Livewire\Patients\Edit as PatientsEdit;
use App\Livewire\MedicalRecords\Create as MedicalRecordsCreate;
use App\Livewire\MedicalRecords\ViewRecord as MedicalRecordsView;
use App\Actions\Reports\GeneratePrescriptionPdfAction;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;

Route::prefix('patients')->name('patients.')->group(function () {
    Route::get('/', PatientsIndex::class)->name('index');
    Route::get('/create', PatientsCreate::class)->name('create');
    Route::get('/{patient}', PatientsShow::class)->name('show');
    Route::get('/{patient}/edit', PatientsEdit::class)->name('edit');
});

// Rutas de Historias Clínicas y Documentos
Route::prefix('patients/{patient}/medical-records')->name('medical-records.')->group(function () {
    Route::get('/create', MedicalRecordsCreate::class)->name('create');
    Route::get('/{record}', MedicalRecordsView::class)->name('view');
});

Route::prefix('medical-records/{record}/prescription')->name('medical-records.prescription.')->group(function () {
    Route::get('/download', function (MedicalRecord $record) {
        Gate::authorize('view', $record);
        return (new GeneratePrescriptionPdfAction())->download($record);
    })->name('download');

    Route::get('/stream', function (MedicalRecord $record) {
        Gate::authorize('view', $record);
        return (new GeneratePrescriptionPdfAction())->stream($record);
    })->name('stream');
});
