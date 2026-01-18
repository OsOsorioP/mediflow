<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\MedicalRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MedicalRecordPdf extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public MedicalRecord $record
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address'),
                $this->record->clinic->name
            ),
            subject: 'Registro Médico - ' . $this->record->clinic->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.medical-records.pdf',
            with: [
                'record' => $this->record,
                'patient' => $this->record->patient,
                'clinic' => $this->record->clinic,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Generar el PDF
        $pdf = Pdf::loadView('pdfs.prescription', [
            'record' => $this->record,
            'patient' => $this->record->patient,
            'doctor' => $this->record->creator,
            'clinic' => $this->record->clinic,
        ]);

        $filename = sprintf(
            'receta_%s_%s.pdf',
            $this->record->patient->identification_number,
            $this->record->consultation_date->format('Y-m-d')
        );

        return [
            Attachment::fromData(fn () => $pdf->output(), $filename)
                ->withMime('application/pdf'),
        ];
    }
}