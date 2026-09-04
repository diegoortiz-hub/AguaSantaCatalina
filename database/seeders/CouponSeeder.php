<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'codigo'        => 'PURASALUD10',
                'tipo'          => 'porcentaje',
                'descuento'     => 10.00,
                'minimo_compra' => 0,
                'maximo_usos'   => null,
                'usos_actuales' => 0,
                'activo'        => true,
                'vence_en'      => null,
            ],
            [
                'codigo'        => 'BIENVENIDO',
                'tipo'          => 'porcentaje',
                'descuento'     => 10.00,
                'minimo_compra' => 0,
                'maximo_usos'   => null,
                'usos_actuales' => 0,
                'activo'        => true,
                'vence_en'      => null,
            ],
            [
                'codigo'        => 'SANTACATALINA',
                'tipo'          => 'porcentaje',
                'descuento'     => 10.00,
                'minimo_compra' => 0,
                'maximo_usos'   => null,
                'usos_actuales' => 0,
                'activo'        => true,
                'vence_en'      => null,
            ],
            [
                'codigo'        => 'AGUA15',
                'tipo'          => 'porcentaje',
                'descuento'     => 15.00,
                'minimo_compra' => 0,
                'maximo_usos'   => 500,
                'usos_actuales' => 0,
                'activo'        => true,
                'vence_en'      => now()->addMonths(6),
            ],
        ];

        foreach ($coupons as $coupon) {
            DB::table('coupons')->updateOrInsert(
                ['codigo' => $coupon['codigo']],
                array_merge($coupon, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $this->command->info('✓ 4 cupones insertados/actualizados.');
    }
}
