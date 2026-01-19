<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;

class HealthCheckController extends Controller
{
    public function __invoke()
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'queue' => $this->checkQueue(),
            'storage' => $this->checkStorage(),
        ];
        
        $healthy = collect($checks)->every(fn($check) => $check['status'] === 'ok');
        
        return response()->json([
            'status' => $healthy ? 'healthy' : 'unhealthy',
            'checks' => $checks,
            'timestamp' => now()->toIso8601String(),
        ], $healthy ? 200 : 503);
    }
    
    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'ok', 'message' => 'Database connected'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    private function checkCache(): array
    {
        try {
            Cache::put('health_check', true, 10);
            $value = Cache::get('health_check');
            return ['status' => $value ? 'ok' : 'error'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    private function checkQueue(): array
    {
        try {
            $size = Queue::size();
            return [
                'status' => 'ok',
                'pending_jobs' => $size,
                'warning' => $size > 100 ? 'High queue size' : null,
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    private function checkStorage(): array
    {
        $path = storage_path('logs');
        $free = disk_free_space($path);
        $total = disk_total_space($path);
        $used_percent = (($total - $free) / $total) * 100;
        
        return [
            'status' => $used_percent < 90 ? 'ok' : 'warning',
            'disk_usage_percent' => round($used_percent, 2),
        ];
    }
}