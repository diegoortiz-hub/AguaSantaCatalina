<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/**
 * Lectura de la analítica propia. Todo sale de las tablas agregadas por día,
 * así que son consultas pequeñas incluso con mucho tráfico acumulado.
 */
class Analitica
{
    public function __construct(private int $dias = 30) {}

    public function paraUltimos(int $dias): static
    {
        return new static($dias);
    }

    private function desde(): string
    {
        return now()->subDays($this->dias - 1)->toDateString();
    }

    public function visitas(): int
    {
        return (int) DB::table('visitas')->where('fecha', '>=', $this->desde())->sum('visitas');
    }

    public function visitantes(): int
    {
        return DB::table('visitantes')->where('fecha', '>=', $this->desde())->distinct('token')->count('token');
    }

    public function visitasHoy(): int
    {
        return (int) DB::table('visitas')->where('fecha', now()->toDateString())->sum('visitas');
    }

    public function visitantesHoy(): int
    {
        return DB::table('visitantes')->where('fecha', now()->toDateString())->count();
    }

    /** Páginas más vistas del período. */
    public function paginasTop(int $limite = 8): Collection
    {
        return DB::table('visitas')
            ->selectRaw('ruta, SUM(visitas) as total')
            ->where('fecha', '>=', $this->desde())
            ->groupBy('ruta')
            ->orderByDesc('total')
            ->limit($limite)
            ->get();
    }

    /** De dónde llegan las visitas que vienen de fuera del sitio. */
    public function origenesTop(int $limite = 6): Collection
    {
        return DB::table('referencias')
            ->selectRaw('origen, SUM(visitas) as total')
            ->where('fecha', '>=', $this->desde())
            ->groupBy('origen')
            ->orderByDesc('total')
            ->limit($limite)
            ->get();
    }

    /** Serie diaria de visitas, con los días sin tráfico en cero. */
    public function serieDiaria(): array
    {
        $porDia = DB::table('visitas')
            ->selectRaw('fecha, SUM(visitas) as total')
            ->where('fecha', '>=', $this->desde())
            ->groupBy('fecha')
            ->pluck('total', 'fecha');

        $serie = [];
        for ($i = $this->dias - 1; $i >= 0; $i--) {
            $serie[] = (int) ($porDia[now()->subDays($i)->toDateString()] ?? 0);
        }

        return $serie;
    }

    public function hayDatos(): bool
    {
        return DB::table('visitas')->exists();
    }

    /**
     * Cuántos pedidos se generaron por cada 100 visitantes del período.
     * Es la métrica que conecta el tráfico con el negocio.
     */
    public function conversion(): ?float
    {
        $visitantes = $this->visitantes();

        if ($visitantes === 0) {
            return null;
        }

        $pedidos = DB::table('orders')
            ->where('estado', '!=', 'cancelado')
            ->where('created_at', '>=', $this->desde().' 00:00:00')
            ->count();

        return round(($pedidos / $visitantes) * 100, 2);
    }
}
