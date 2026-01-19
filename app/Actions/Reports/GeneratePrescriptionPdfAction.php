<?php

declare(strict_types=1);

namespace App\Actions\Reports;

use App\Models\MedicalRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class GeneratePrescriptionPdfAction
{
    public function execute(MedicalRecord $record): string
    {
        $record->load(['patient', 'creator', 'clinic']);

        $pdf = Pdf::loadView('pdfs.prescription', [
            'record' => $record,
            'patient' => $record->patient,
            'doctor' => $record->creator,
            'clinic' => $record->clinic,
        ]);

        $pdf->setPaper('letter');

        $filename = sprintf(
            'receta_%s_%s.pdf',
            $record->patient->identification_number,
            $record->consultation_date->format('Y-m-d')
        );

        $path = "prescriptions/{$record->clinic_id}/{$filename}";
        Storage::disk('local')->put($path, $pdf->output());

        return $path;
    }

    public function download(MedicalRecord $record): \Illuminate\Http\Response
    {
        $record->load(['patient', 'creator', 'clinic']);

        $pdf = Pdf::loadView('pdfs.prescription', [
            'record' => $record,
            'patient' => $record->patient,
            'doctor' => $record->creator,
            'clinic' => $record->clinic,
        ]);

        $filename = sprintf(
            'receta_%s_%s.pdf',
            $record->patient->full_name,
            $record->consultation_date->format('Y-m-d')
        );

        return $pdf->download($filename);
    }

    public function stream(MedicalRecord $record): \Illuminate\Http\Response
    {
        $record->load(['patient', 'creator', 'clinic']);

        $pdf = Pdf::loadView('pdfs.prescription', [
            'record' => $record,
            'patient' => $record->patient,
            'doctor' => $record->creator,
            'clinic' => $record->clinic,
        ]);

        return $pdf->stream();
    }
}