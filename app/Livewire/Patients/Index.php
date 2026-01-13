<?php

declare(strict_types=1);

namespace App\Livewire\Patients;

use App\Models\Patient;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, AuthorizesRequests;

    // Propiedades públicas (son reactivas)
    public string $search = '';
    public string $filterGender = '';
    public bool $filterActive = true;
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    // Listeners para eventos de otros componentes
    protected $listeners = ['patientCreated' => '$refresh', 'patientUpdated' => '$refresh'];

    /**
     * Resetear paginación cuando cambia la búsqueda
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Resetear paginación cuando cambian los filtros
     */
    public function updatedFilterGender(): void
    {
        $this->resetPage();
    }

    public function updatedFilterActive(): void
    {
        $this->resetPage();
    }

    /**
     * Ordenar por columna
     */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Archivar/Desarchivar paciente
     */
    public function toggleActive(int $patientId): void
    {
        $patient = Patient::findOrFail($patientId);
        
        $this->authorize('archive', $patient);

        $patient->update([
            'is_active' => !$patient->is_active,
        ]);

        $message = $patient->is_active ? 'Paciente activado' : 'Paciente archivado';
        session()->flash('message', $message);
    }

    /**
     * Eliminar paciente (soft delete)
     */
    public function delete(int $patientId): void
    {
        $patient = Patient::findOrFail($patientId);
        
        $this->authorize('delete', $patient);

        $patient->delete();

        session()->flash('message', 'Paciente eliminado correctamente');
    }

    /**
     * Renderizar el componente
     */
    public function render(): View
    {
        $this->authorize('viewAny', Patient::class);

        $patients = Patient::query()
            ->when($this->search, function ($query) {
                $query->search($this->search);
            })
            ->when($this->filterGender, function ($query) {
                $query->gender($this->filterGender);
            })
            ->when($this->filterActive !== '', function ($query) {
                $query->where('is_active', $this->filterActive);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view('livewire.patients.index', [
            'patients' => $patients,
        ]);
    }
}