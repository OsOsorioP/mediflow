<?php

declare(strict_types=1);

namespace App\Actions\Reports;

use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateReceiptPdfAction
{

    public function download(Payment $payment): \Illuminate\Http\Response
    {
        $payment->load(['patient', 'creator', 'clinic', 'appointment']);

        $pdf = Pdf::loadView('pdfs.receipt', [
            'payment' => $payment,
            'patient' => $payment->patient,
            'clinic' => $payment->clinic,
        ]);

        $filename = sprintf(
            'recibo_%s_%s.pdf',
            $payment->payment_number,
            $payment->payment_date->format('Y-m-d')
        );

        return $pdf->download($filename);
    }

    public function stream(Payment $payment): \Illuminate\Http\Response
    {
        $payment->load(['patient', 'creator', 'clinic', 'appointment']);

        $pdf = Pdf::loadView('pdfs.receipt', [
            'payment' => $payment,
            'patient' => $payment->patient,
            'clinic' => $payment->clinic,
        ]);

        return $pdf->stream();
    }
}