<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
        // Si no hay usuario autenticado, continuar (el middleware 'auth' se encarga)
        if (! $request->user()) {
            return $next($request);
        }

        $user = $request->user();

        // Verificar que el usuario tenga una clínica asignada
        if (! $user->clinic_id) {
            abort(403, 'Usuario sin clínica asignada. Contacte al administrador.');
        }

        // Verificar que el usuario esté activo
        if (! $user->is_active) {
            auth()->logout();

            return redirect()->route('login')
                ->withErrors(['email' => 'Tu cuenta ha sido desactivada. Contacta al administrador.']);
        }

        // Cargar la relación de clínica si no está cargada (optimización)
        if (! $user->relationLoaded('clinic')) {
            $user->load('clinic');
        }

        // Verificar que la clínica esté activa
        if (! $user->clinic || ! $user->clinic->is_active) {
            auth()->logout();

            return redirect()->route('login')
                ->withErrors(['email' => 'La clínica ha sido suspendida. Contacta a soporte.']);
        }

        // Opcional: Compartir la clínica con todas las vistas
        view()->share('currentClinic', $user->clinic);

        return $next($request);
    }
}
