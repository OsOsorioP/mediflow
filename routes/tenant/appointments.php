<?php

use App\Livewire\Appointments\Index as AppointmentsIndex;
use App\Livewire\Appointments\Create as AppointmentsCreate;
use App\Livewire\Appointments\Edit as AppointmentsEdit;
use Illuminate\Support\Facades\Route;

Route::prefix('appointments')->name('appointments.')->group(function () {
    Route::get('/', AppointmentsIndex::class)->name('index');
    Route::get('/create', AppointmentsCreate::class)->name('create');
    Route::get('/{appointment}/edit', AppointmentsEdit::class)->name('edit');
});
