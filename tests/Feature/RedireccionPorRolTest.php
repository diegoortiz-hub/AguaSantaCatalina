<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Al iniciar sesión todos caían en /mi-cuenta, incluido el administrador, que
 * quedaba mirando la vista de cliente sin ningún enlace al panel. Y Auth::attempt
 * no comprobaba "activo", así que una cuenta dada de baja seguía entrando a la
 * tienda: sólo el panel lo verificaba.
 */
class RedireccionPorRolTest extends TestCase
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

    public function test_el_administrador_aterriza_en_el_panel(): void
    {
        $admin = $this->usuario('admin');

        $this->post('/login', ['email' => $admin->email, 'password' => 'secreto12'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_el_cliente_aterriza_en_su_cuenta(): void
    {
        $cliente = $this->usuario('cliente');

        $this->post('/login', ['email' => $cliente->email, 'password' => 'secreto12'])
            ->assertRedirect(route('mi-cuenta'));

        $this->assertAuthenticatedAs($cliente);
    }

    public function test_la_pagina_que_se_pedia_antes_gana_a_la_redireccion_por_rol(): void
    {
        $admin = $this->usuario('admin');

        // Pedir /admin sin sesión deja la url guardada en intended.
        $this->get('/admin')->assertRedirect('/admin/acceso');

        $this->post('/login', ['email' => $admin->email, 'password' => 'secreto12'])
            ->assertRedirect(url('/admin'));
    }

    public function test_una_cuenta_desactivada_no_inicia_sesion(): void
    {
        $baja = $this->usuario('cliente', activo: false);

        $this->from('/login')
            ->post('/login', ['email' => $baja->email, 'password' => 'secreto12'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_un_administrador_desactivado_tampoco_inicia_sesion(): void
    {
        $baja = $this->usuario('admin', activo: false);

        $this->post('/login', ['email' => $baja->email, 'password' => 'secreto12']);

        $this->assertGuest();
    }

    public function test_el_enlace_al_panel_sale_en_la_cabecera_solo_para_el_administrador(): void
    {
        $this->actingAs($this->usuario('admin'))
            ->get('/')
            ->assertOk()
            ->assertSee(route('admin.dashboard'));

        $this->actingAs($this->usuario('cliente'))
            ->get('/')
            ->assertOk()
            ->assertDontSee(route('admin.dashboard'));
    }

    public function test_la_barra_de_mi_cuenta_lleva_al_panel_solo_al_administrador(): void
    {
        $this->actingAs($this->usuario('admin'))
            ->get('/mi-cuenta')
            ->assertOk()
            ->assertSee('Panel de administración');

        $this->actingAs($this->usuario('cliente'))
            ->get('/mi-cuenta')
            ->assertOk()
            ->assertDontSee('Panel de administración');
    }
    /**
     * La barra del panel traía una tarjeta de WhatsApp con el número de la
     * propia tienda: pulsarla abría una conversación del administrador
     * consigo mismo. Y no existía ningún enlace de vuelta a la tienda.
     */
    public function test_el_panel_enlaza_a_la_tienda_y_no_al_whatsapp_de_la_propia_tienda(): void
    {
        $this->actingAs($this->usuario('admin'))
            ->get('/admin')
            ->assertOk()
            ->assertSee('Ver tienda online')
            ->assertDontSee('wa.me');
    }
}
