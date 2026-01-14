<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware: EnsureTenantContext
 *
 * Garantiza que:
 * 1. El usuario autenticado pertenezca a una clínica activa
 * 2. El usuario esté activo
 * 3. Establece el contexto del tenant para toda la request
 */
class EnsureTenantContext
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Obtener el usuario de la request
        $user = $request->user();

        // Si no hay usuario autenticado, continuar (el middleware 'auth' se encargará después)
        if (! $user) {
            return $next($request);
        }

        // 2. Establecer el ID de la clínica en el TenantManager (Paso vital para el Global Scope)
        if ($user->clinic_id) {
            app(TenantManager::class)->setClinicId($user->clinic_id);
        } else {
            // Si el usuario no tiene clínica, es un error de configuración de cuenta
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Tu cuenta no tiene una clínica asignada.']);
        }

        // 3. Verificar si el usuario está activo
        if (! $user->is_active) {
            Auth::logout(); // <-- Cambiado de auth()->logout() a Auth::logout()
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Tu cuenta ha sido desactivada. Contacta al administrador.']);
        }

        // 4. Cargar y verificar la clínica
        $user->loadMissing('clinic');

        if (! $user->clinic || ! $user->clinic->is_active) {
            Auth::logout(); // <-- Cambiado de auth()->logout() a Auth::logout()
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'La clínica se encuentra inactiva o suspendida.']);
        }

        // 5. Compartir la clínica con todas las vistas (opcional, muy útil para Blade)
        view()->share('currentClinic', $user->clinic);

        return $next($request);
    }
}
