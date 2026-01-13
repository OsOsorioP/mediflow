<?php

use App\Livewire\Patients\Index as PatientsIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rutas de pacientes
    Route::get('/patients', PatientsIndex::class)->name('patients.index');
    
    // Estas las crearemos después
    // Route::get('/patients/{patient}', Show::class)->name('patients.show');
});

require __DIR__.'/auth.php';