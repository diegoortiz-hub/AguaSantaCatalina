<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * El panel entraba por /login, que es la página de la tienda: trae el bloque
 * "Crear cuenta gratis" y el acceso por WhatsApp al lado. Ahora tiene su
 * propia puerta en /admin/acceso, y ahí sólo pasa un administrador activo.
 */
class PuertaDelPanelTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_la_puerta_del_panel_se_ve_sin_sesion_y_no_ofrece_registro(): void
    {
        $respuesta = $this->get('/admin/acceso');

        $respuesta->assertOk()
            ->assertSee('Panel de administración')
            ->assertSee('Entrar al panel')
            ->assertDontSee('Crear cuenta gratis')
            ->assertDontSee('Pedir sin cuenta');

        // Una puerta de administración no tiene por qué estar indexada.
        $respuesta->assertSee('noindex, nofollow', false);
    }

    public function test_el_administrador_entra_por_su_puerta(): void
    {
        $admin = $this->usuario('admin');

        $this->post('/admin/acceso', ['email' => $admin->email, 'password' => 'secreto12'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_un_cliente_con_credenciales_correctas_no_pasa_ni_queda_con_sesion(): void
    {
        $cliente = $this->usuario('cliente');

        $this->from('/admin/acceso')
            ->post('/admin/acceso', ['email' => $cliente->email, 'password' => 'secreto12'])
            ->assertRedirect('/admin/acceso')
            ->assertSessionHasErrors('email');

        // Lo importante: no basta con no redirigirlo, no puede quedar dentro.
        $this->assertGuest();
    }

    public function test_un_administrador_desactivado_no_pasa(): void
    {
        $baja = $this->usuario('admin', activo: false);

        $this->from('/admin/acceso')
            ->post('/admin/acceso', ['email' => $baja->email, 'password' => 'secreto12'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_credenciales_erroneas_no_pasan(): void
    {
        $admin = $this->usuario('admin');

        $this->from('/admin/acceso')
            ->post('/admin/acceso', ['email' => $admin->email, 'password' => 'incorrecta'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_un_administrador_con_sesion_no_vuelve_a_ver_el_formulario(): void
    {
        $this->actingAs($this->usuario('admin'))
            ->get('/admin/acceso')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_cerrar_sesion_desde_el_panel_devuelve_a_la_puerta_del_panel(): void
    {
        $this->actingAs($this->usuario('admin'))
            ->post('/admin/salir')
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();
    }

    public function test_la_puerta_lleva_a_la_seccion_que_se_pedia_antes(): void
    {
        $admin = $this->usuario('admin');

        $this->get('/admin/pedidos')->assertRedirect('/admin/acceso');

        $this->post('/admin/acceso', ['email' => $admin->email, 'password' => 'secreto12'])
            ->assertRedirect(url('/admin/pedidos'));
    }

    public function test_un_invitado_de_la_tienda_sigue_yendo_al_login_de_la_tienda(): void
    {
        // El cambio de redirección es sólo para /admin: mi-cuenta no se toca.
        $this->get('/mi-cuenta')->assertRedirect('/login');
    }
}
