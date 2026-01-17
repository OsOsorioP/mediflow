<?php

declare(strict_types=1);

namespace App\Actions\Reports;

use App\Models\MedicalRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class GeneratePrescriptionPdfAction
{
    /**
     * Genera un PDF de receta médica
     */
    public function execute(MedicalRecord $record): string
    {
        // Cargar relaciones necesarias
        $record->load(['patient', 'creator', 'clinic']);

        // Generar el PDF
        $pdf = Pdf::loadView('pdfs.prescription', [
            'record' => $record,
            'patient' => $record->patient,
            'doctor' => $record->creator,
            'clinic' => $record->clinic,
        ]);

        // Configurar opciones
        $pdf->setPaper('letter');

        // Generar nombre del archivo
        $filename = sprintf(
            'receta_%s_%s.pdf',
            $record->patient->identification_number,
            $record->consultation_date->format('Y-m-d')
        );

        // Guardar en storage (opcional)
        $path = "prescriptions/{$record->clinic_id}/{$filename}";
        Storage::disk('local')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Descarga directa del PDF
     */
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

    /**
     * Stream del PDF (mostrar en navegador)
     */
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