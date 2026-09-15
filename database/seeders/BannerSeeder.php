<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

/**
 * Los cuatro banners que entregó el cliente, listos para el carrusel de la home.
 *
 * Se crean DESACTIVADOS a propósito: activar uno cambia por completo la portada,
 * y esa es una decisión del negocio, no del despliegue. Se prenden desde
 * Admin → Banners.
 */
class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'titulo'    => "Si existe magia contenida en el agua\nésta se encuentra en Aguas Santa Catalina",
                'subtitulo' => 'Dispensadores, bombas y recargas con despacho en Santiago.',
                'imagen'    => 'banners/banner-1.jpg',
                'link'      => '/productos',
                'orden'     => 1,
            ],
            [
                'titulo'    => 'Cuida a los tuyos siempre',
                'subtitulo' => 'Agua purificada extraída de los cauces superficiales de la cordillera de los Andes.',
                'imagen'    => 'banners/banner-2.jpg',
                'link'      => '/nosotros',
                'orden'     => 2,
            ],
            [
                // Ojo: el precio va escrito dentro de la imagen. Si cambia la
                // oferta hay que rehacer el banner, no se edita desde el panel.
                'titulo'    => 'Pack 12 litros + dispensador USB',
                'subtitulo' => 'Incluye envases y despacho gratis.',
                'imagen'    => 'banners/banner-3.jpg',
                'link'      => '/ofertas',
                'orden'     => 3,
            ],
            [
                'titulo'    => 'Pack agua purificada',
                'subtitulo' => 'Bomba USB Premium más 3 bidones, con envases incluidos.',
                'imagen'    => 'banners/banner-4.jpg',
                'link'      => '/ofertas',
                'orden'     => 4,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::updateOrCreate(
                ['imagen' => $banner['imagen']],
                $banner + ['activo' => false]
            );
        }

        $this->command->info('BannerSeeder: 4 banners cargados (desactivados, se prenden desde el panel).');
    }
}
