<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class TenantManager
{
    private ?int $clinicId = null;

    public function setClinicId(int $id): void
    {
        $this->clinicId = $id;
    }

    public function getClinicId(): ?int
    {

        return $this->clinicId;
    }

    public function hasClinic(): bool
    {
        return $this->getClinicId() !== null;
    }
}
