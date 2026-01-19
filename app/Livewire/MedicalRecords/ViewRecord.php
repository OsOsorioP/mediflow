<?php

declare(strict_types=1);

namespace App\Livewire\MedicalRecords;

use App\Jobs\SendMedicalRecordPdf;
use App\Models\MedicalRecord;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ViewRecord extends Component
{
    use AuthorizesRequests;

    public MedicalRecord $record;
    public string $emailRecipient = '';
    public bool $showEmailForm = false;

    public function mount(MedicalRecord $record): void
    {
        $this->record = $record;
        $this->record->load(['patient', 'creator', 'clinic']);

        $this->authorize('view', $this->record);

        $this->emailRecipient = $this->record->patient->email ?? '';

        $this->record->auditView();
    }

    public function toggleEmailForm(): void
    {
        $this->showEmailForm = !$this->showEmailForm;
    }

    public function sendPdfByEmail(): void
    {
        $this->validate([
            'emailRecipient' => 'required|email',
        ], [
            'emailRecipient.required' => 'El email es obligatorio',
            'emailRecipient.email' => 'Debe ser un email válido',
        ]);

        SendMedicalRecordPdf::dispatch($this->record, $this->emailRecipient);

        session()->flash('message', 'El PDF se enviará por email a ' . $this->emailRecipient);
        
        $this->showEmailForm = false;
    }

    public function render(): View
    {
        return view('livewire.medical-records.view');
    }
}
