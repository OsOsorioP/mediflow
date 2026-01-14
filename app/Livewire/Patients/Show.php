<?php

declare(strict_types=1);

namespace App\Livewire\Patients;

use App\Models\Patient;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination, AuthorizesRequests;

    public Patient $patient;
    public string $activeTab = 'info'; // info, medical_records, audit

    // Listeners para refrescar cuando se crea/actualiza un registro médico
    protected $listeners = [
        'medicalRecordCreated' => '$refresh',
        'medicalRecordUpdated' => '$refresh',
    ];

    /**
     * Mount - Se ejecuta al inicializar el componente
     */
    public function mount(Patient $patient): void
    {
        $this->authorize('view', $patient);
        
        // Auditar que se visualizó el paciente
        $patient->auditView();
        
        $this->patient = $patient;
    }

    /**
     * Cambiar de pestaña
     */
    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    /**
     * Renderizar componente
     */
    public function render(): View
    {
        // Cargar registros médicos con paginación
        $medicalRecords = $this->patient
            ->medicalRecords()
            ->with('creator')
            ->recent()
            ->paginate(10);

        // Cargar logs de auditoría si está en esa pestaña
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