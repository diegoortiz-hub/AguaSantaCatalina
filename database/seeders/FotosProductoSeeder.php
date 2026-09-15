<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Asocia las fotos del cliente a los productos.
 *
 * Sólo se asignan las correspondencias reales. Tres productos del catálogo
 * quedan sin foto a propósito porque ninguna imagen entregada los muestra:
 * el bidón de 12 litros (sólo aparece sobre el fondo de una cocina, en un
 * banner), la base metálica y el filtro de sedimentos.
 */
class FotosProductoSeeder extends Seeder
{
    private const FOTOS = [
        'bidon-20l-agua-purificada'           => 'bidon-20l.jpg',
        'dispensador-electrico-frio-caliente' => 'sobremesa-electrico.jpg',
        'dispensador-pedestal-premium'        => 'compresor-pedestal-conexion-red.jpg',
        'bomba-usb-recargable'                => 'dispensador-pro-touch.jpg',
        'pack-2-bidones-20l'                  => 'pack-usb-2-cargas.jpg',
        'pack-familiar-4-bidones-20l'         => 'pack-usb-4-cargas.jpg',
    ];

    public function run(): void
    {
        $asignadas = 0;
        $sinArchivo = [];

        foreach (self::FOTOS as $slug => $archivo) {
            $ruta = "productos/{$archivo}";

            if (! Storage::disk('public')->exists($ruta)) {
                $sinArchivo[] = $archivo;
                continue;
            }

            // No pisa una foto subida desde el panel.
            $afectados = Product::where('slug', $slug)
                ->where(fn ($q) => $q->whereNull('imagen')->orWhere('imagen', ''))
                ->update(['imagen' => $ruta]);

            $asignadas += $afectados;
        }

        $this->command->info("FotosProductoSeeder: {$asignadas} productos con foto.");

        if ($sinArchivo) {
            $this->command->warn('Faltan en el disco público: '.implode(', ', $sinArchivo).'. Corre MediaSeeder primero.');
        }

        $pendientes = Product::whereNull('imagen')->orWhere('imagen', '')->pluck('nombre');

        if ($pendientes->isNotEmpty()) {
            $this->command->line('Sin foto todavía: '.$pendientes->implode(' · '));
        }
    }
}
