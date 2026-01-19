<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class TelemetryService
{
    public function logAction(string $action, array $context = []): void
    {
        Log::channel('telemetry')->info($action, [
            'user_id' => auth()->id(),
            'clinic_id' => app(TenantManager::class)->getClinicId(),
            'timestamp' => now(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            ...$context,
        ]);
    }
    
    public function logPerformance(string $operation, float $duration): void
    {
        Log::channel('performance')->info($operation, [
            'duration_ms' => $duration,
            'memory_mb' => memory_get_usage(true) / 1024 / 1024,
        ]);
    }
    
    public function logError(\Throwable $e, array $context = []): void
    {
        Log::channel('errors')->error($e->getMessage(), [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            ...$context,
        ]);
    }
}