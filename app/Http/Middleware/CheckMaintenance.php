<?php

namespace App\Http\Middleware;

use App\Support\Settings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenance
{
    public function __construct(private Settings $settings) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Admin y login siempre pasan
        if ($request->is('admin*') || $request->is('login*') || $request->is('logout*')) {
            return $next($request);
        }

        $settings = $this->settings->all();

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
