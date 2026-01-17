<?php

declare(strict_types=1);

namespace App\Livewire\MedicalRecords;

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

    /**
     * Mount
     */
    public function mount(MedicalRecord $record): void
    {
        $this->record = $record;
        $this->record->load(['patient', 'creator', 'clinic']);

        $this->authorize('view', $this->record);

        // Auditar visualización
        $this->record->auditView();
    }

    /**
     * Renderizar
     */
    public function render(): View
    {
        return view('livewire.medical-records.view');
    }
}
