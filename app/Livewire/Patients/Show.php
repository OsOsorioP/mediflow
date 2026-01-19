<?php

declare(strict_types=1);

namespace App\Livewire\Patients;

use App\Models\Patient;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class Show extends Component
{
    use WithPagination, AuthorizesRequests;

    #[Layout('layouts.app')]

    public Patient $patient;
    public string $activeTab = 'info';

    protected $listeners = [
        'medicalRecordCreated' => '$refresh',
        'medicalRecordUpdated' => '$refresh',
    ];

    public function mount(Patient $patient): void
    {
        $this->authorize('view', $patient);
        
        $patient->auditView();
        
        $this->patient = $patient;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function render(): View
    {
        $medicalRecords = $this->patient
            ->medicalRecords()
            ->with('creator')
            ->recent()
            ->paginate(10);

        $auditLogs = null;
        if ($this->activeTab === 'audit') {
            $auditLogs = $this->patient
                ->auditLogs()
                ->with('user')
                ->recent()
                ->paginate(20);
        }

        return view('livewire.patients.show', [
            'medicalRecords' => $medicalRecords,
            'auditLogs' => $auditLogs,
        ]);
    }
}