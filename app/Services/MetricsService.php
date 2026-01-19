<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class MetricsService
{
    public function trackAppointmentCreated(): void
    {
        $this->increment('appointments.created.total');
        $this->increment('appointments.created.today');
    }
    
    public function trackPaymentReceived(float $amount): void
    {
        $this->increment('payments.received.total');
        $this->add('revenue.total', $amount);
        $this->add('revenue.today', $amount);
    }
    
    public function trackLogin(): void
    {
        $this->increment('logins.total');
        $this->increment('logins.today');
    }
    
    private function increment(string $key, int $amount = 1): void
    {
        Cache::increment($key, $amount);
    }
    
    private function add(string $key, float $value): void
    {
        $current = Cache::get($key, 0);
        Cache::put($key, $current + $value);
    }
    
    public function getMetrics(): array
    {
        return [
            'appointments' => [
                'total' => Cache::get('appointments.created.total', 0),
                'today' => Cache::get('appointments.created.today', 0),
            ],
            'revenue' => [
                'total' => Cache::get('revenue.total', 0),
                'today' => Cache::get('revenue.today', 0),
            ],
            'logins' => [
                'total' => Cache::get('logins.total', 0),
                'today' => Cache::get('logins.today', 0),
            ],
        ];
    }
}