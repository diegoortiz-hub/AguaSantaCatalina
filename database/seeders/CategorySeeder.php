<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'nombre'      => 'Agua Purificada',
                'slug'        => 'agua-purificada',
                'descripcion' => 'Bidones y garrafas de agua purificada de alta calidad para consumo humano.',
                'imagen'      => null,
                'activo'      => true,
                'orden'       => 1,
            ],
            [
                'nombre'      => 'Dispensadores',
                'slug'        => 'dispensadores',
                'descripcion' => 'Dispensadores de agua eléctricos y de mesa para hogar y oficina.',
                'imagen'      => null,
                'activo'      => true,
                'orden'       => 2,
            ],
            [
                'nombre'      => 'Bombas',
                'slug'        => 'bombas',
                'descripcion' => 'Bombas manuales y eléctricas para dispensar agua de bidones.',
                'imagen'      => null,
                'activo'      => true,
                'orden'       => 3,
            ],
            [
                'nombre'      => 'Accesorios',
                'slug'        => 'accesorios',
                'descripcion' => 'Bases, soportes y accesorios complementarios para tus bidones.',
                'imagen'      => null,
                'activo'      => true,
                'orden'       => 4,
            ],
            [
                'nombre'      => 'Repuestos y Filtros',
                'slug'        => 'repuestos-y-filtros',
                'descripcion' => 'Filtros de sedimentos, repuestos y piezas de mantenimiento.',
                'imagen'      => null,
                'activo'      => true,
                'orden'       => 5,
            ],
            [
                'nombre'      => 'Packs y Promos',
                'slug'        => 'packs-y-promos',
                'descripcion' => 'Ofertas especiales y paquetes con descuento para ahorro máximo.',
                'imagen'      => null,
                'activo'      => true,
                'orden'       => 6,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }

        $this->command->info('✅ CategorySeeder: 6 categorías creadas.');
    }
}
