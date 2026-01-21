<?php

use App\Livewire\Dashboard\Index as DashboardIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'tenant', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardIndex::class)->name('dashboard');
    Route::view('profile', 'profile')->name('profile');

    // Rutas Modulares por Dominio
    require __DIR__ . '/tenant/users.php';
    require __DIR__ . '/tenant/patients.php';
    require __DIR__ . '/tenant/appointments.php';
    require __DIR__ . '/tenant/payments.php';
});

require __DIR__ . '/auth.php';
