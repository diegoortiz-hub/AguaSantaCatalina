<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * El contenido del sitio (páginas y menús) pasó de estar escrito en el layout a
 * administrarse desde el panel. Estas pruebas cubren que el sitio siga en pie
 * cuando ese contenido cambia, y que un enlace no pueda apuntar donde no debe.
 */
class PaginasYMenusTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'nombre'   => 'Admin',
            'email'    => 'admin-'.uniqid().'@test.cl',
            'password' => Hash::make('secreto12'),
            'rol'      => 'admin',
            'activo'   => true,
        ]);
    }

    private function pagina(array $extra = []): Page
    {
        return Page::create(array_merge([
            'titulo'    => 'Preguntas Frecuentes',
            'slug'      => 'preguntas-frecuentes',
            'contenido' => '<p>Entre 24 y 48 horas hábiles.</p>',
            'activo'    => true,
        ], $extra));
    }

    // ── Páginas ────────────────────────────────────────────────────────────

    public function test_una_pagina_publicada_se_ve_en_su_direccion(): void
    {
        $this->pagina();

        $this->get('/preguntas-frecuentes')
            ->assertOk()
            ->assertSee('Preguntas Frecuentes')
            ->assertSee('24 y 48 horas', false);
    }

    public function test_una_pagina_en_borrador_devuelve_404(): void
    {
        $this->pagina(['activo' => false]);

        $this->get('/preguntas-frecuentes')->assertNotFound();
    }

    public function test_el_comodin_no_se_come_las_rutas_de_la_tienda(): void
    {
        // Si el comodín /{slug} quedara antes que las rutas reales, estas caerían.
        foreach (['/productos', '/ofertas', '/empresas', '/nosotros', '/contacto', '/carrito', '/login'] as $ruta) {
            $this->get($ruta)->assertOk("La ruta {$ruta} dejó de responder");
        }
    }

    public function test_el_slug_se_normaliza_al_guardar(): void
    {
        $page = Page::create(['titulo' => 'Cómo Comprar', 'slug' => 'Cómo Comprar', 'activo' => true]);

        $this->assertSame('como-comprar', $page->slug);
    }

    public function test_el_admin_no_puede_ocupar_una_direccion_del_sistema(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.paginas.store'), [
                'titulo' => 'Falsa', 'slug' => 'productos', 'activo' => '1',
            ])
            ->assertSessionHasErrors('slug');

        $this->assertDatabaseMissing('pages', ['slug' => 'productos']);
    }

    public function test_no_se_permiten_dos_paginas_con_la_misma_direccion(): void
    {
        $this->pagina();

        $this->actingAs($this->admin())
            ->post(route('admin.paginas.store'), [
                'titulo' => 'Otra', 'slug' => 'preguntas-frecuentes', 'activo' => '1',
            ])
            ->assertSessionHasErrors('slug');
    }

    // ── Menús ──────────────────────────────────────────────────────────────

    public function test_el_menu_del_header_sale_de_la_base_de_datos(): void
    {
        MenuItem::create([
            'ubicacion' => 'header', 'etiqueta' => 'Inicio',
            'tipo' => 'ruta', 'destino' => 'home', 'orden' => 1, 'activo' => true,
        ]);

        $this->get('/')->assertOk()->assertSee('INICIO', false);
    }

    public function test_un_enlace_oculto_no_se_pinta(): void
    {
        MenuItem::create([
            'ubicacion' => 'header', 'etiqueta' => 'Secreto',
            'tipo' => 'ruta', 'destino' => 'home', 'orden' => 1, 'activo' => false,
        ]);

        $this->get('/')->assertOk()->assertDontSee('SECRETO', false);
    }

    public function test_un_enlace_a_una_pagina_borrada_no_rompe_el_sitio(): void
    {
        $page = $this->pagina();

        MenuItem::create([
            'ubicacion' => 'header', 'etiqueta' => 'Preguntas',
            'tipo' => 'pagina', 'destino' => $page->slug, 'orden' => 1, 'activo' => true,
        ]);

        $page->delete();
        MenuItem::olvidarCache();

        // El enlace queda huérfano: debe desaparecer, no tumbar la página.
        $this->get('/')->assertOk()->assertDontSee('PREGUNTAS', false);
    }

    public function test_el_admin_no_puede_apuntar_un_enlace_a_una_ruta_fuera_de_la_lista(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.menus.store'), [
                'ubicacion' => 'header', 'etiqueta' => 'Panel',
                'tipo' => 'ruta', 'destino' => 'admin.dashboard',
            ])
            ->assertSessionHasErrors('destino');

        $this->assertDatabaseMissing('menu_items', ['destino' => 'admin.dashboard']);
    }

    public function test_el_admin_no_puede_apuntar_a_una_pagina_inexistente(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.menus.store'), [
                'ubicacion' => 'footer_1', 'etiqueta' => 'Fantasma',
                'tipo' => 'pagina', 'destino' => 'no-existe',
            ])
            ->assertSessionHasErrors('destino');
    }

    public function test_un_enlace_de_categoria_apunta_al_catalogo_filtrado(): void
    {
        Category::create(['nombre' => 'Bombas', 'slug' => 'bombas', 'activo' => true]);

        $item = MenuItem::create([
            'ubicacion' => 'footer_1', 'etiqueta' => 'Bombas',
            'tipo' => 'categoria', 'destino' => 'bombas', 'orden' => 1, 'activo' => true,
        ]);

        $this->assertStringContainsString('categoria=bombas', $item->url());
    }

    public function test_guardar_un_enlace_invalida_la_cache_del_menu(): void
    {
        $this->get('/')->assertOk(); // deja el menú en caché

        MenuItem::create([
            'ubicacion' => 'header', 'etiqueta' => 'Nuevo',
            'tipo' => 'ruta', 'destino' => 'ofertas', 'orden' => 9, 'activo' => true,
        ]);

        $this->get('/')->assertOk()->assertSee('NUEVO', false);
    }

    public function test_el_menu_cacheado_devuelve_modelos_y_no_basura(): void
    {
        MenuItem::create([
            'ubicacion' => 'header', 'etiqueta' => 'Inicio',
            'tipo' => 'ruta', 'destino' => 'home', 'orden' => 1, 'activo' => true,
        ]);

        MenuItem::de('header');            // primera llamada: consulta y cachea
        $desdeCache = MenuItem::de('header'); // segunda: sale de caché

        $this->assertInstanceOf(MenuItem::class, $desdeCache->first());
        $this->assertSame('Inicio', $desdeCache->first()->etiqueta);
        $this->assertNotNull($desdeCache->first()->url());
    }
}
