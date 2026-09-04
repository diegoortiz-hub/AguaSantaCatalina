<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        // Admin y login siempre pasan
        if ($request->is('admin*') || $request->is('login*') || $request->is('logout*')) {
            return $next($request);
        }

        $path = storage_path('app/settings.json');
        if (! file_exists($path)) {
            return $next($request);
        }

        $settings = json_decode(file_get_contents($path), true) ?? [];

        if (empty($settings['mantencion_activa'])) {
            return $next($request);
        }

        // Admins autenticados pueden ver la tienda igualmente
        if (auth()->check() && auth()->user()->es_admin) {
            return $next($request);
        }

        return response()->view('maintenance', [
            'mensaje' => $settings['mantencion_mensaje'] ?? 'Estamos realizando mejoras. Volvemos pronto.',
            'fin'     => $settings['mantencion_fin'] ?? null,
        ], 503);
    }
}
