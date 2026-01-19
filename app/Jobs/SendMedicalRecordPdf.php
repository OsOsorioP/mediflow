<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\MedicalRecordPdf;
use App\Models\MedicalRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMedicalRecordPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(
        public MedicalRecord $record,
        public string $recipientEmail
    ) {
        //
    }

    public function handle(): void
    {
        Mail::to($this->recipientEmail)
            ->send(new MedicalRecordPdf($this->record));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Error enviando PDF de registro médico', [
            'record_id' => $this->record->id,
            'email' => $this->recipientEmail,
            'error' => $exception->getMessage(),
        ]);
    }
}