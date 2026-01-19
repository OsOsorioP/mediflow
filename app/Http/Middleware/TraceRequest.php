<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TraceRequest
{
    public function handle(Request $request, Closure $next)
    {
        $traceId = Str::uuid()->toString();
        $request->attributes->set('trace_id', $traceId);
        
        $startTime = microtime(true);
        
        Log::info('Request started', [
            'trace_id' => $traceId,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
        ]);
        
        $response = $next($request);
        
        $duration = (microtime(true) - $startTime) * 1000;
        
        Log::info('Request completed', [
            'trace_id' => $traceId,
            'status' => $response->status(),
            'duration_ms' => round($duration, 2),
        ]);
        
        $response->headers->set('X-Trace-ID', $traceId);
        
        return $response;
    }
}