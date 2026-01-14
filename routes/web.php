<?php

use App\Livewire\Patients\Index as PatientsIndex;
use App\Livewire\Patients\Show as PatientsShow;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'tenant', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::view('profile', 'profile')->name('profile');

    // Ruta correcta apuntando al componente Livewire
    Route::get('/patients', PatientsIndex::class)->name('patients.index');

    Route::get('/patients/create', \App\Livewire\Patients\Create::class)->name('patients.create');

    Route::get('/patients/{patient}', PatientsShow::class)->name('patients.show');
});

require __DIR__.'/auth.php';