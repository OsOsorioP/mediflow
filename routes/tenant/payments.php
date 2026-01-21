<?php

use App\Livewire\Payments\Index as PaymentsIndex;
use App\Livewire\Payments\Create as PaymentsCreate;
use App\Actions\Reports\GenerateReceiptPdfAction;
use App\Models\Payment;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;

Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('/', PaymentsIndex::class)->name('index');
    Route::get('/create', PaymentsCreate::class)->name('create');

    Route::get('/{payment}/receipt/download', function (Payment $payment) {
        Gate::authorize('view', $payment);
        return (new GenerateReceiptPdfAction())->download($payment);
    })->name('receipt.download');
});
