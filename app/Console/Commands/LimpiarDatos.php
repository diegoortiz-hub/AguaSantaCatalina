<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LimpiarDatos extends Command
{
    protected $signature = 'mantencion:limpiar {--dias=90 : Antigüedad a partir de la cual se borra}';

    protected $description = 'Poda datos que crecen sin límite: visitantes, carritos abandonados y trabajos fallidos antiguos';

    public function handle(): int
    {
        $dias  = max(7, (int) $this->option('dias'));
        $corte = now()->subDays($dias);

        // Los tokens de visitante sólo sirven para contar únicos del día; pasado
        // el período de análisis no aportan nada y son la tabla que más crece.
        $visitantes = DB::table('visitantes')->where('fecha', '<', $corte->toDateString())->delete();

        // Un carrito que nadie tocó en dos semanas es un carrito abandonado.
        $carritos = DB::table('cart_items')->where('updated_at', '<', now()->subDays(14))->delete();

        $fallidos = DB::table('failed_jobs')->where('failed_at', '<', $corte)->delete();

        $this->info("Visitantes podados: {$visitantes}");
        $this->info("Carritos abandonados eliminados: {$carritos}");
        $this->info("Trabajos fallidos antiguos: {$fallidos}");

        return self::SUCCESS;
    }
}
