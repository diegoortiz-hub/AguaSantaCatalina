<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RegistrarVisita
{
    /** Tramos que no son páginas del sitio y ensuciarían las métricas. */
    private const EXCLUIDOS = ['admin', 'api', 'storage', 'build', 'up', 'livewire'];

    /** Señales de rastreadores. No busca ser exhaustivo, sólo quitar el grueso. */
    private const BOTS = [
        'bot', 'crawl', 'spider', 'slurp', 'curl', 'wget', 'python', 'headless',
        'lighthouse', 'pagespeed', 'preview', 'monitor', 'uptime', 'facebookexternalhit',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Se registra en terminate() y no en handle(): la escritura ocurre después
     * de haber enviado la respuesta, así la analítica no le agrega latencia a
     * ninguna página.
     */
    public function terminate(Request $request, Response $response): void
    {
        try {
            if (! $this->contabilizable($request, $response)) {
                return;
            }

            $hoy  = now()->toDateString();
            $ruta = $this->ruta($request);

            DB::table('visitas')->upsert(
                ['fecha' => $hoy, 'ruta' => $ruta, 'visitas' => 1],
                ['fecha', 'ruta'],
                ['visitas' => DB::raw('visitas + 1')]
            );

            DB::table('visitantes')->insertOrIgnore([
                'fecha' => $hoy,
                'token' => $this->token($request, $hoy),
            ]);

            if ($origen = $this->origen($request)) {
                DB::table('referencias')->upsert(
                    ['fecha' => $hoy, 'origen' => $origen, 'visitas' => 1],
                    ['fecha', 'origen'],
                    ['visitas' => DB::raw('visitas + 1')]
                );
            }
        } catch (\Throwable) {
            // Una métrica perdida no vale romper una página.
        }
    }

    private function contabilizable(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET') || $response->getStatusCode() !== 200) {
            return false;
        }

        // Sólo páginas: deja fuera JSON, imágenes y descargas.
        if (! Str::contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return false;
        }

        if ($request->is(...array_map(fn ($p) => $p.'*', self::EXCLUIDOS))) {
            return false;
        }

        // El equipo mirando su propia tienda no es tráfico.
        if ($request->user()?->isAdmin()) {
            return false;
        }

        $agente = Str::lower((string) $request->userAgent());

        return $agente !== '' && ! Str::contains($agente, self::BOTS);
    }

    /** Ruta normalizada, sin query string y acotada en largo. */
    private function ruta(Request $request): string
    {
        $ruta = '/'.trim($request->path(), '/');

        return Str::limit($ruta === '/' ? '/' : rtrim($ruta, '/'), 185, '');
    }

    /**
     * Identificador pseudónimo que cambia cada día. Incluye APP_KEY para que no
     * pueda recalcularse desde fuera, y la fecha para que no siga a nadie entre
     * días. No se guarda la IP en ninguna parte.
     */
    private function token(Request $request, string $fecha): string
    {
        return hash('sha256', implode('|', [
            $request->ip(),
            $request->userAgent(),
            $fecha,
            config('app.key'),
        ]));
    }

    /** Dominio desde el que llegó la visita, si vino de fuera del sitio. */
    private function origen(Request $request): ?string
    {
        $referer = $request->headers->get('referer');

        if (! $referer) {
            return null;
        }

        $host = parse_url($referer, PHP_URL_HOST);

        if (! $host || $host === $request->getHost()) {
            return null;
        }

        return Str::limit(Str::lower($host), 185, '');
    }
}
