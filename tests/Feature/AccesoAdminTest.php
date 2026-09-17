<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * El grupo /admin estuvo protegido sólo por 'auth' sin comprobar el rol, así que
 * cualquier cliente registrado entraba al panel completo. Esta prueba evita que
 * la comprobación vuelva a caerse sin que nadie lo note.
 */
class AccesoAdminTest extends TestCase
{
    use RefreshDatabase;

    private const RUTAS_ADMIN = [
        '/admin',
        '/admin/productos',
        '/admin/pedidos',
        '/admin/clientes',
        '/admin/cupones',
        '/admin/banners',
        '/admin/estadisticas',
        '/admin/configuracion',
    ];

    private function usuario(string $rol, bool $activo = true): User
    {
        return User::create([
            'nombre'   => 'Usuario '.$rol,
            'email'    => $rol.'-'.uniqid().'@test.cl',
            'password' => Hash::make('secreto12'),
            'rol'      => $rol,
            'activo'   => $activo,
        ]);
    }

    public function test_un_cliente_autenticado_no_entra_a_ninguna_seccion_del_panel(): void
    {
        $cliente = $this->usuario('cliente');

        foreach (self::RUTAS_ADMIN as $ruta) {
            $this->actingAs($cliente)->get($ruta)
                ->assertForbidden("El cliente no debería acceder a {$ruta}");
        }
    }

    public function test_un_visitante_sin_sesion_es_enviado_a_la_puerta_del_panel(): void
    {
        // No al formulario de la tienda: el panel tiene su propia puerta.
        $this->get('/admin')->assertRedirect('/admin/acceso');
    }

    public function test_el_administrador_entra_a_todas_las_secciones(): void
    {
        $admin = $this->usuario('admin');

        foreach (self::RUTAS_ADMIN as $ruta) {
            $this->actingAs($admin)->get($ruta)
                ->assertOk("El administrador debería acceder a {$ruta}");
        }
    }

    public function test_un_administrador_desactivado_queda_fuera(): void
    {
        $admin = $this->usuario('admin', activo: false);

        $this->actingAs($admin)->get('/admin')->assertForbidden();
    }

    public function test_el_cliente_conserva_su_propia_area(): void
    {
        $cliente = $this->usuario('cliente');

        $this->actingAs($cliente)->get('/mi-cuenta')->assertOk();
    }
}
