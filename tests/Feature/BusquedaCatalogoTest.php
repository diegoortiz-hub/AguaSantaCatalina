<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * El buscador devolvía cero resultados al escribir un plural: "aguas" no
 * encontraba "Agua Purificada", y quien buscaba así concluía que no funcionaba.
 */
class BusquedaCatalogoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $agua = Category::create(['nombre' => 'Agua Purificada', 'slug' => 'agua-purificada', 'activo' => true]);
        $disp = Category::create(['nombre' => 'Dispensadores', 'slug' => 'dispensadores', 'activo' => true]);
        $rep  = Category::create(['nombre' => 'Repuestos y Filtros', 'slug' => 'repuestos', 'activo' => true]);

        $productos = [
            [$agua->id, 'Bidón 20L Agua Purificada', 'bidon-20l', 'Agua por ósmosis inversa.', 'SKU-B20'],
            [$agua->id, 'Bidón 12L Agua Purificada', 'bidon-12l', 'Formato compacto.', 'SKU-B12'],
            [$disp->id, 'Dispensador Eléctrico Frío/Caliente', 'disp-electrico', 'Compresor silencioso.', 'SKU-DE1'],
            [$rep->id,  'Filtro Sedimentos 5 Micras', 'filtro-5', 'Repuesto anual.', 'SKU-F05'],
        ];

        foreach ($productos as [$cat, $nombre, $slug, $desc, $sku]) {
            Product::create([
                'category_id' => $cat, 'nombre' => $nombre, 'slug' => $slug,
                'descripcion' => $desc, 'sku' => $sku,
                'precio' => 3990, 'stock' => 10, 'activo' => true,
            ]);
        }
    }

    private function buscar(string $q): \Illuminate\Testing\TestResponse
    {
        return $this->get('/productos?q='.urlencode($q));
    }

    /** Extrae los slugs de producto enlazados en la página. */
    private function slugsEncontrados(string $q): array
    {
        preg_match_all('#/productos/([a-z0-9\-]+)"#', $this->buscar($q)->getContent(), $coincidencias);

        $slugs = array_unique($coincidencias[1]);
        sort($slugs);

        return $slugs;
    }

    public function test_el_plural_devuelve_exactamente_lo_mismo_que_el_singular(): void
    {
        $pares = [
            ['agua', 'aguas'],
            ['bidon', 'bidones'],
            ['dispensador', 'dispensadores'],
            ['filtro', 'filtros'],
        ];

        foreach ($pares as [$singular, $plural]) {
            $esperado = $this->slugsEncontrados($singular);

            $this->assertNotEmpty($esperado, "«{$singular}» no encontró nada, la prueba no vale");
            $this->assertSame($esperado, $this->slugsEncontrados($plural),
                "«{$plural}» debería encontrar lo mismo que «{$singular}»");
        }
    }

    public function test_aguas_encuentra_los_productos_de_agua(): void
    {
        $this->buscar('aguas')
            ->assertOk()
            ->assertSee('Bidón 20L Agua Purificada', false)
            ->assertSee('Bidón 12L Agua Purificada', false);
    }

    public function test_busca_tambien_por_nombre_de_categoria(): void
    {
        $this->buscar('Repuestos')
            ->assertOk()
            ->assertSee('Filtro Sedimentos', false);
    }

    public function test_busca_por_sku(): void
    {
        $this->buscar('SKU-DE1')
            ->assertOk()
            ->assertSee('Dispensador Eléctrico', false);
    }

    public function test_con_varias_palabras_exige_que_aparezcan_todas(): void
    {
        $this->buscar('bidon 12L')
            ->assertOk()
            ->assertSee('Bidón 12L', false)
            ->assertDontSee('Dispensador Eléctrico', false);
    }

    public function test_un_termino_inexistente_muestra_el_estado_vacio_con_sugerencias(): void
    {
        $this->buscar('zapatillas')
            ->assertOk()
            ->assertSee('Nada coincide con', false)
            ->assertSee('Dispensadores', false); // categorías sugeridas
    }

    public function test_los_comodines_de_like_no_devuelven_el_catalogo_completo(): void
    {
        // Sin escapar, un "%" haría que LIKE '%%%' coincidiera con todo.
        foreach (['%', '%%', '_', '\\'] as $comodin) {
            $this->buscar($comodin)
                ->assertOk()
                ->assertSee('Nada coincide con', false);
        }
    }

    public function test_el_buscador_esta_disponible_en_movil_y_en_escritorio(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        // Dos formularios: el del header (oculto bajo 768px) y el de móvil.
        $this->assertSame(2, substr_count($html, 'name="q"'),
            'Debe haber un buscador para escritorio y otro para móvil');
    }

    public function test_solo_queda_un_boton_flotante_de_whatsapp_en_la_navegacion(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        // Antes había uno en el header y otro en la barra de navegación, además
        // del flotante. En el layout debe quedar sólo el flotante.
        $this->assertStringContainsString('id="wa-float"', $html);
        $this->assertStringNotContainsString('Pedir ahora', $html);
        $this->assertStringNotContainsString('WHATSAPP', $html);
    }
}
