<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\User;
use App\Support\Analitica;
use App\Support\EstadoSistema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SistemaYAnaliticaTest extends TestCase
{
    use RefreshDatabase;

    private const NAVEGADOR = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0 Safari/537.36';

    protected function setUp(): void
    {
        parent::setUp();

        $categoria = Category::create(['nombre' => 'Agua', 'slug' => 'agua', 'activo' => true]);
        Product::create([
            'category_id' => $categoria->id, 'nombre' => 'Bidón 20L', 'slug' => 'bidon-20l',
            'precio' => 3990, 'stock' => 20, 'activo' => true,
        ]);
    }

    private function visitar(string $ruta, array $cabeceras = []): void
    {
        // El kernel ya ejecuta terminate() del middleware al cerrar la petición,
        // así que basta con hacer la visita.
        $this->withHeaders(array_merge(['User-Agent' => self::NAVEGADOR], $cabeceras))->get($ruta);
    }

    private function admin(): User
    {
        return User::create([
            'nombre' => 'Admin', 'email' => 'admin-'.uniqid().'@test.cl',
            'password' => Hash::make('secreto12'), 'rol' => 'admin', 'activo' => true,
        ]);
    }

    // ── Analítica ──────────────────────────────────────────────────────────

    public function test_cuenta_una_visita_por_carga_agrupando_por_ruta(): void
    {
        $this->visitar('/');
        $this->visitar('/');
        $this->visitar('/productos');

        $this->assertDatabaseHas('visitas', ['ruta' => '/', 'visitas' => 2]);
        $this->assertDatabaseHas('visitas', ['ruta' => '/productos', 'visitas' => 1]);
    }

    public function test_no_crea_una_fila_por_visita_sino_un_contador_por_dia(): void
    {
        foreach (range(1, 12) as $i) {
            $this->visitar('/');
        }

        // Lo que crece es el contador, no la cantidad de filas.
        $this->assertSame(1, \DB::table('visitas')->where('ruta', '/')->count());
        $this->assertDatabaseHas('visitas', ['ruta' => '/', 'visitas' => 12]);
    }

    public function test_no_cuenta_a_los_rastreadores(): void
    {
        $this->visitar('/', ['User-Agent' => 'Googlebot/2.1 (+http://www.google.com/bot.html)']);
        $this->visitar('/', ['User-Agent' => 'curl/8.4.0']);

        $this->assertDatabaseCount('visitas', 0);
    }

    public function test_no_cuenta_el_trafico_del_equipo(): void
    {
        $this->actingAs($this->admin());
        $this->visitar('/');

        $this->assertDatabaseCount('visitas', 0);
    }

    public function test_no_cuenta_paginas_que_no_existen(): void
    {
        $this->visitar('/esta-pagina-no-existe');

        $this->assertDatabaseCount('visitas', 0);
    }

    public function test_guarda_de_donde_llego_la_visita_pero_no_el_trafico_propio(): void
    {
        $this->visitar('/', ['Referer' => 'https://www.google.com/search?q=agua']);
        $this->visitar('/productos', ['Referer' => 'http://localhost/']);

        $this->assertDatabaseHas('referencias', ['origen' => 'www.google.com', 'visitas' => 1]);
        $this->assertDatabaseCount('referencias', 1);
    }

    public function test_no_guarda_ip_ni_navegador_en_ninguna_parte(): void
    {
        $this->visitar('/');

        $columnas = \Schema::getColumnListing('visitantes');

        $this->assertSame(['id', 'fecha', 'token'], $columnas,
            'La tabla de visitantes no debe tener columnas para datos personales');

        // El token es un hash, no el dato original.
        $token = \DB::table('visitantes')->value('token');
        $this->assertSame(64, strlen($token));
        $this->assertStringNotContainsString('127.0.0.1', $token);
    }

    public function test_el_mismo_visitante_no_se_cuenta_dos_veces_en_el_dia(): void
    {
        $this->visitar('/');
        $this->visitar('/productos');
        $this->visitar('/ofertas');

        $this->assertDatabaseCount('visitantes', 1);
    }

    public function test_la_lectura_agrega_visitas_y_rankea_paginas(): void
    {
        $this->visitar('/');
        $this->visitar('/');
        $this->visitar('/productos');

        $analitica = app(Analitica::class)->paraUltimos(30);

        $this->assertSame(3, $analitica->visitas());
        $this->assertSame(1, $analitica->visitantes());
        $this->assertSame('/', $analitica->paginasTop()->first()->ruta);
        $this->assertTrue($analitica->hayDatos());
    }

    public function test_la_limpieza_borra_visitantes_antiguos_y_conserva_los_recientes(): void
    {
        \DB::table('visitantes')->insert([
            ['fecha' => now()->subDays(120)->toDateString(), 'token' => str_repeat('a', 64)],
            ['fecha' => now()->toDateString(),               'token' => str_repeat('b', 64)],
        ]);

        $this->artisan('mantencion:limpiar', ['--dias' => 90])->assertSuccessful();

        $this->assertDatabaseCount('visitantes', 1);
        $this->assertDatabaseHas('visitantes', ['token' => str_repeat('b', 64)]);
    }

    // ── Estado del sistema ─────────────────────────────────────────────────

    public function test_comprueba_la_salud_sin_lanzar_excepciones(): void
    {
        $comprobaciones = app(EstadoSistema::class)->todo();

        foreach (['base_datos', 'almacenamiento', 'cache', 'cola', 'correo', 'migraciones', 'entorno', 'version'] as $clave) {
            $this->assertArrayHasKey($clave, $comprobaciones);
            $this->assertContains($comprobaciones[$clave]['estado'], [EstadoSistema::OK, EstadoSistema::AVISO, EstadoSistema::FALLA]);
            $this->assertNotEmpty($comprobaciones[$clave]['valor']);
        }
    }

    public function test_avisa_que_el_correo_no_sale_cuando_va_al_log(): void
    {
        config(['mail.default' => 'log']);

        $correo = app(EstadoSistema::class)->todo()['correo'];

        $this->assertSame(EstadoSistema::FALLA, $correo['estado']);
    }

    public function test_marca_falla_si_produccion_corre_con_depuracion_activa(): void
    {
        config(['app.env' => 'production', 'app.debug' => true]);

        $entorno = app(EstadoSistema::class)->todo()['entorno'];

        $this->assertSame(EstadoSistema::FALLA, $entorno['estado']);
    }

    public function test_el_resumen_se_queda_con_el_peor_estado(): void
    {
        $estado = app(EstadoSistema::class);

        $this->assertSame(EstadoSistema::OK, $estado->resumen([
            ['estado' => EstadoSistema::OK], ['estado' => EstadoSistema::OK],
        ]));
        $this->assertSame(EstadoSistema::AVISO, $estado->resumen([
            ['estado' => EstadoSistema::OK], ['estado' => EstadoSistema::AVISO],
        ]));
        $this->assertSame(EstadoSistema::FALLA, $estado->resumen([
            ['estado' => EstadoSistema::AVISO], ['estado' => EstadoSistema::FALLA],
        ]));
    }

    public function test_el_modulo_solo_lo_ve_el_administrador(): void
    {
        $cliente = User::create([
            'nombre' => 'Cliente', 'email' => 'c-'.uniqid().'@test.cl',
            'password' => Hash::make('secreto12'), 'rol' => 'cliente', 'activo' => true,
        ]);

        $this->actingAs($cliente)->get('/admin/sistema')->assertForbidden();
        $this->actingAs($this->admin())->get('/admin/sistema')->assertOk();
    }

    public function test_el_modulo_funciona_sin_ninguna_visita_registrada(): void
    {
        Page::create(['titulo' => 'Términos', 'slug' => 'terminos', 'activo' => true]);

        $this->actingAs($this->admin())->get('/admin/sistema')
            ->assertOk()
            ->assertSee('Todavía no hay visitas registradas', false);
    }
}
