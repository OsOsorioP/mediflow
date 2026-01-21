<?php

use App\Livewire\Users\Index as UsersIndex;
use App\Livewire\Users\Create as UsersCreate;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', UsersIndex::class)->name('index');
    Route::get('/create', UsersCreate::class)->name('create');
});
