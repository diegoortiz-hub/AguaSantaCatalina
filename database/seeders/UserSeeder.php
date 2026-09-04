<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ─────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@aguassantacatalina.cl'],
            [
                'nombre'   => 'Administrador',
                'email'    => 'admin@aguassantacatalina.cl',
                'password' => Hash::make('Admin2026!'),
                'rut'      => null,
                'telefono' => '+56 9 9999 0000',
                'rol'      => 'admin',
                'activo'   => true,
            ]
        );

        // ── Clientes de prueba ────────────────────────────────
        $clientes = [
            [
                'nombre'   => 'María González',
                'email'    => 'maria@example.cl',
                'password' => Hash::make('cliente123'),
                'rut'      => '12.345.678-9',
                'telefono' => '+56 9 8765 4321',
                'rol'      => 'cliente',
                'activo'   => true,
            ],
            [
                'nombre'   => 'Carlos Muñoz',
                'email'    => 'carlos@example.cl',
                'password' => Hash::make('cliente123'),
                'rut'      => '9.876.543-2',
                'telefono' => '+56 9 1234 5678',
                'rol'      => 'cliente',
                'activo'   => true,
            ],
            [
                'nombre'   => 'Ana Rodríguez',
                'email'    => 'ana@example.cl',
                'password' => Hash::make('cliente123'),
                'rut'      => '15.432.167-8',
                'telefono' => '+56 9 5555 7777',
                'rol'      => 'cliente',
                'activo'   => true,
            ],
        ];

        foreach ($clientes as $cliente) {
            User::updateOrCreate(['email' => $cliente['email']], $cliente);
        }

        $this->command->info('✅ UserSeeder: 1 admin + 3 clientes creados.');
        $this->command->line('   Admin: admin@aguassantacatalina.cl / Admin2026!');
        $this->command->line('   Clientes: *@example.cl / cliente123');
    }
}
