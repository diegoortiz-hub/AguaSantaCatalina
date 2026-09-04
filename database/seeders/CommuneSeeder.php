<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommuneSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('communes')->truncate();

        $communes = [
            ['name' => 'Las Condes',       'delivery_days' => 'Lunes a Sábado',                        'free_shipping_threshold' => 15000, 'standard_shipping_cost' => 2500, 'delivery_time' => 'Mismo día o 24 hrs', 'orden' => 1],
            ['name' => 'Providencia',       'delivery_days' => 'Lunes a Sábado',                        'free_shipping_threshold' => 15000, 'standard_shipping_cost' => 2500, 'delivery_time' => 'Mismo día o 24 hrs', 'orden' => 2],
            ['name' => 'Vitacura',          'delivery_days' => 'Lunes a Sábado',                        'free_shipping_threshold' => 15000, 'standard_shipping_cost' => 2500, 'delivery_time' => 'Mismo día o 24 hrs', 'orden' => 3],
            ['name' => 'Lo Barnechea',      'delivery_days' => 'Lunes a Sábado',                        'free_shipping_threshold' => 15000, 'standard_shipping_cost' => 2500, 'delivery_time' => '24 a 48 hrs',       'orden' => 4],
            ['name' => 'Ñuñoa',             'delivery_days' => 'Lunes a Sábado',                        'free_shipping_threshold' => 15000, 'standard_shipping_cost' => 2500, 'delivery_time' => 'Mismo día o 24 hrs', 'orden' => 5],
            ['name' => 'Santiago Centro',   'delivery_days' => 'Lunes a Sábado',                        'free_shipping_threshold' => 15000, 'standard_shipping_cost' => 2500, 'delivery_time' => 'Mismo día o 24 hrs', 'orden' => 6],
            ['name' => 'La Reina',          'delivery_days' => 'Lunes a Sábado',                        'free_shipping_threshold' => 15000, 'standard_shipping_cost' => 2500, 'delivery_time' => '24 hrs',             'orden' => 7],
            ['name' => 'Peñalolén',         'delivery_days' => 'Lunes, Miércoles y Viernes',            'free_shipping_threshold' => 15000, 'standard_shipping_cost' => 2990, 'delivery_time' => '24 a 48 hrs',       'orden' => 8],
            ['name' => 'La Florida',        'delivery_days' => 'Lunes a Sábado',                        'free_shipping_threshold' => 15000, 'standard_shipping_cost' => 2500, 'delivery_time' => '24 hrs',             'orden' => 9],
            ['name' => 'Macul',             'delivery_days' => 'Lunes a Sábado',                        'free_shipping_threshold' => 15000, 'standard_shipping_cost' => 2500, 'delivery_time' => '24 hrs',             'orden' => 10],
            ['name' => 'San Miguel',        'delivery_days' => 'Lunes a Sábado',                        'free_shipping_threshold' => 15000, 'standard_shipping_cost' => 2500, 'delivery_time' => '24 hrs',             'orden' => 11],
            ['name' => 'Maipú',             'delivery_days' => 'Lunes, Martes, Jueves, Sábado',         'free_shipping_threshold' => 15000, 'standard_shipping_cost' => 2990, 'delivery_time' => '24 a 48 hrs',       'orden' => 12],
            ['name' => 'Huechuraba',        'delivery_days' => 'Martes, Jueves y Sábado',               'free_shipping_threshold' => 15000, 'standard_shipping_cost' => 2990, 'delivery_time' => '24 a 48 hrs',       'orden' => 13],
            ['name' => 'Quilicura',         'delivery_days' => 'Martes, Jueves y Sábado',               'free_shipping_threshold' => 15000, 'standard_shipping_cost' => 2990, 'delivery_time' => '24 a 48 hrs',       'orden' => 14],
            ['name' => 'Colina / Chicureo', 'delivery_days' => 'Lunes, Miércoles y Viernes',            'free_shipping_threshold' => 20000, 'standard_shipping_cost' => 3500, 'delivery_time' => '24 a 48 hrs',       'orden' => 15],
            ['name' => 'Puente Alto',       'delivery_days' => 'Lunes, Miércoles y Viernes',            'free_shipping_threshold' => 15000, 'standard_shipping_cost' => 2990, 'delivery_time' => '24 a 48 hrs',       'orden' => 16],
        ];

        foreach ($communes as $commune) {
            DB::table('communes')->insert(array_merge($commune, [
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('✓ 16 comunas insertadas correctamente.');
    }
}
