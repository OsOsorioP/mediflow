<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureTenantContext
{

    public function handle(Request $request, Closure $next): Response
    {

        $user = $request->user();


        if (! $user) {
            return $next($request);
        }

        if ($user->clinic_id) {
            app(TenantManager::class)->setClinicId($user->clinic_id);
        } else {

            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Tu cuenta no tiene una clínica asignada.']);
        }

        if (! $user->is_active) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Tu cuenta ha sido desactivada. Contacta al administrador.']);
        }

        $user->loadMissing('clinic');

        if (! $user->clinic || ! $user->clinic->is_active) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'La clínica se encuentra inactiva o suspendida.']);
        }

        view()->share('currentClinic', $user->clinic);

        return $next($request);
    }
}
