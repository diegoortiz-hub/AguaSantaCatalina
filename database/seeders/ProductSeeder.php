<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $aguaPurificada   = Category::where('slug', 'agua-purificada')->first();
        $dispensadores    = Category::where('slug', 'dispensadores')->first();
        $bombas           = Category::where('slug', 'bombas')->first();
        $accesorios       = Category::where('slug', 'accesorios')->first();
        $repuestos        = Category::where('slug', 'repuestos-y-filtros')->first();
        $packs            = Category::where('slug', 'packs-y-promos')->first();

        $products = [
            // ── Agua Purificada ───────────────────────────────────
            [
                'category_id'     => $aguaPurificada->id,
                'nombre'          => 'Bidón 20L Agua Purificada',
                'slug'            => 'bidon-20l-agua-purificada',
                'descripcion'     => 'Bidón retornable de 20 litros con agua purificada por osmosis inversa. Apto para consumo humano, libre de bacterias y contaminantes. Compatible con todos los dispensadores estándar.',
                'precio'          => 3990,
                'precio_original' => null,
                'stock'           => 142,
                'stock_minimo'    => 10,
                'sku'             => 'ASC-B20L',
                'imagen'          => null,
                'imagenes'        => null,
                'badge'           => 'Más vendido',
                'badge_color'     => 'blue',
                'destacado'       => true,
                'activo'          => true,
                'specs'           => [
                    'Volumen'    => '20 litros',
                    'Material'   => 'PET retornable',
                    'Pureza'     => 'Osmosis inversa',
                    'Apto'       => 'Consumo humano',
                ],
            ],
            [
                'category_id'     => $aguaPurificada->id,
                'nombre'          => 'Bidón 12L Agua Purificada',
                'slug'            => 'bidon-12l-agua-purificada',
                'descripcion'     => 'Bidón de 12 litros ideal para espacios reducidos o menor consumo. Agua purificada de la misma calidad, más manejable y fácil de transportar.',
                'precio'          => 3290,
                'precio_original' => 3890,
                'stock'           => 3,
                'stock_minimo'    => 5,
                'sku'             => 'ASC-B12L',
                'imagen'          => null,
                'imagenes'        => null,
                'badge'           => 'Pocas unidades',
                'badge_color'     => 'orange',
                'destacado'       => false,
                'activo'          => true,
                'specs'           => [
                    'Volumen'    => '12 litros',
                    'Material'   => 'PET retornable',
                    'Pureza'     => 'Osmosis inversa',
                    'Apto'       => 'Consumo humano',
                ],
            ],

            // ── Dispensadores ─────────────────────────────────────
            [
                'category_id'     => $dispensadores->id,
                'nombre'          => 'Dispensador Eléctrico Frío/Caliente',
                'slug'            => 'dispensador-electrico-frio-caliente',
                'descripcion'     => 'Dispensador eléctrico de sobremesa con doble función: agua fría (4–10°C) y agua caliente (85–95°C) instantánea. Ideal para hogar y oficina. Compatible con bidones de 20L.',
                'precio'          => 42490,
                'precio_original' => 54990,
                'stock'           => 18,
                'stock_minimo'    => 3,
                'sku'             => 'ASC-DE-FC',
                'imagen'          => null,
                'imagenes'        => null,
                'badge'           => '23% off',
                'badge_color'     => 'red',
                'destacado'       => true,
                'activo'          => true,
                'specs'           => [
                    'Voltaje'          => '220V / 50Hz',
                    'Potencia fría'    => '65W',
                    'Potencia caliente'=> '500W',
                    'Temp. fría'       => '4 – 10 °C',
                    'Temp. caliente'   => '85 – 95 °C',
                    'Compatibilidad'   => 'Bidones 20L',
                    'Garantía'         => '12 meses',
                ],
            ],
            [
                'category_id'     => $dispensadores->id,
                'nombre'          => 'Dispensador Pedestal Premium',
                'slug'            => 'dispensador-pedestal-premium',
                'descripcion'     => 'Dispensador de pie con diseño moderno y elegante. Triple temperatura: frío, ambiente y caliente. Pantalla LED, auto-limpieza UV y capacidad para bidones de 20L. La opción premium para tu espacio.',
                'precio'          => 89990,
                'precio_original' => 109990,
                'stock'           => 18,
                'stock_minimo'    => 2,
                'sku'             => 'ASC-DP-PREM',
                'imagen'          => null,
                'imagenes'        => null,
                'badge'           => 'Premium',
                'badge_color'     => 'purple',
                'destacado'       => true,
                'activo'          => true,
                'specs'           => [
                    'Tipo'             => 'Pedestal (pie)',
                    'Temperatura'      => 'Frío / Ambiente / Caliente',
                    'UV auto-limpieza' => 'Sí',
                    'Pantalla'         => 'LED',
                    'Voltaje'          => '220V / 50Hz',
                    'Garantía'         => '18 meses',
                ],
            ],

            // ── Bombas ────────────────────────────────────────────
            [
                'category_id'     => $bombas->id,
                'nombre'          => 'Bomba USB Recargable',
                'slug'            => 'bomba-usb-recargable',
                'descripcion'     => 'Bomba eléctrica recargable por USB para bidones de 20L. Sin cables incómodos, carga en 2 horas y entrega hasta 500 dispensaciones por carga. Perfecta para llevar a cualquier lugar.',
                'precio'          => 10390,
                'precio_original' => 12990,
                'stock'           => 45,
                'stock_minimo'    => 5,
                'sku'             => 'ASC-BM-USB',
                'imagen'          => null,
                'imagenes'        => null,
                'badge'           => '20% off',
                'badge_color'     => 'green',
                'destacado'       => true,
                'activo'          => true,
                'specs'           => [
                    'Carga'          => 'USB-C',
                    'Autonomía'      => '~500 dispensaciones',
                    'Tiempo de carga'=> '2 horas',
                    'Compatibilidad' => 'Bidones 20L',
                    'Material'       => 'ABS alimentario',
                ],
            ],

            // ── Accesorios ────────────────────────────────────────
            [
                'category_id'     => $accesorios->id,
                'nombre'          => 'Base Metálica con Válvula',
                'slug'            => 'base-metalica-con-valvula',
                'descripcion'     => 'Base de acero inoxidable con válvula de gravedad incluida. Permite dispensar agua directamente desde el bidón sin bomba. Estable, higiénica y de fácil instalación.',
                'precio'          => 14990,
                'precio_original' => null,
                'stock'           => 2,
                'stock_minimo'    => 3,
                'sku'             => 'ASC-BASE-MV',
                'imagen'          => null,
                'imagenes'        => null,
                'badge'           => 'Últimas unidades',
                'badge_color'     => 'red',
                'destacado'       => false,
                'activo'          => true,
                'specs'           => [
                    'Material'       => 'Acero inoxidable 304',
                    'Incluye'        => 'Válvula de gravedad',
                    'Compatibilidad' => 'Bidones 20L',
                    'Altura'         => '35 cm',
                ],
            ],

            // ── Repuestos y Filtros ───────────────────────────────
            [
                'category_id'     => $repuestos->id,
                'nombre'          => 'Filtro Sedimentos 5 Micras',
                'slug'            => 'filtro-sedimentos-5-micras',
                'descripcion'     => 'Cartucho de filtración de sedimentos con poro de 5 micras. Retiene partículas, arena, óxido y turbidez del agua. Compatible con la mayoría de sistemas de filtración de paso estándar 10".',
                'precio'          => 5490,
                'precio_original' => null,
                'stock'           => 1,
                'stock_minimo'    => 5,
                'sku'             => 'ASC-FLT-5M',
                'imagen'          => null,
                'imagenes'        => null,
                'badge'           => 'Última unidad',
                'badge_color'     => 'red',
                'destacado'       => false,
                'activo'          => true,
                'specs'           => [
                    'Poro'           => '5 micras',
                    'Tamaño'         => '10 pulgadas estándar',
                    'Vida útil'      => '3 – 6 meses',
                    'Material'       => 'Polipropileno',
                ],
            ],

            // ── Packs y Promos ────────────────────────────────────
            [
                'category_id'     => $packs->id,
                'nombre'          => 'Pack 2 Bidones 20L',
                'slug'            => 'pack-2-bidones-20l',
                'descripcion'     => 'Ahorra comprando dos bidones de 20L juntos. Precio especial por pack: $3.590 cada uno en lugar de $3.990. Ideal para familias o semanas de mayor consumo.',
                'precio'          => 7180,
                'precio_original' => 7980,
                'stock'           => 85,
                'stock_minimo'    => 10,
                'sku'             => 'ASC-PACK-2X20',
                'imagen'          => null,
                'imagenes'        => null,
                'badge'           => 'Ahorra $800',
                'badge_color'     => 'green',
                'destacado'       => true,
                'activo'          => true,
                'specs'           => [
                    'Contenido'   => '2 bidones × 20 L',
                    'Precio unit.'=> '$3.590 c/u',
                    'Ahorro'      => '$800 vs precio individual',
                ],
            ],
            [
                'category_id'     => $packs->id,
                'nombre'          => 'Pack Familiar 4 Bidones 20L',
                'slug'            => 'pack-familiar-4-bidones-20l',
                'descripcion'     => 'El mejor precio por bidón del catálogo: $3.498 c/u. Pack de 4 bidones de 20L para familias numerosas o pequeñas empresas. Despacho prioritario incluido.',
                'precio'          => 13990,
                'precio_original' => 15960,
                'stock'           => 150,
                'stock_minimo'    => 10,
                'sku'             => 'ASC-PACK-4X20',
                'imagen'          => null,
                'imagenes'        => null,
                'badge'           => 'Mejor precio',
                'badge_color'     => 'green',
                'destacado'       => true,
                'activo'          => true,
                'specs'           => [
                    'Contenido'   => '4 bidones × 20 L',
                    'Precio unit.'=> '$3.498 c/u',
                    'Ahorro'      => '$1.970 vs precio individual',
                    'Despacho'    => 'Prioritario incluido',
                ],
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['slug' => $product['slug']], $product);
        }

        $this->command->info('✅ ProductSeeder: 9 productos creados.');
    }
}
