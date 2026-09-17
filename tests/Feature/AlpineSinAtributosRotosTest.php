<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Un comentario con comillas dobles dentro de un atributo x-data cierra el
 * atributo antes de tiempo. La página sigue devolviendo 200 y las pruebas de
 * la API siguen pasando, pero en el navegador se cae TODO el componente:
 * ni el carrito, ni los pasos, ni los totales.
 *
 * Pasó de verdad en el checkout y en la calculadora de /empresas. Esta prueba
 * revisa el HTML servido, que es donde el error se nota, y no el Blade.
 */
class AlpineSinAtributosRotosTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('rutasPublicas')]
    public function test_los_atributos_x_data_llegan_completos(string $ruta): void
    {
        $this->prepararCatalogo();

        $html = $this->get($ruta)->assertOk()->getContent();

        foreach ($this->atributosXData($html) as $indice => $expresion) {
            $this->assertStringNotContainsString(
                '"',
                $expresion,
                "El x-data #{$indice} de {$ruta} contiene una comilla doble: el atributo se corta ahí."
            );

            $this->assertSame(
                substr_count($expresion, '{'),
                substr_count($expresion, '}'),
                "El x-data #{$indice} de {$ruta} tiene las llaves desbalanceadas, así que está truncado."
            );
        }
    }

    public function test_el_x_data_del_checkout_llega_hasta_el_final(): void
    {
        $this->prepararCatalogo();

        $html = $this->get('/checkout')->assertOk()->getContent();
        $expresiones = $this->atributosXData($html);

        // El componente grande del checkout es el que trae submitOrder.
        $principal = collect($expresiones)->first(fn ($e) => str_contains($e, 'submitOrder'));

        $this->assertNotNull($principal, 'No se encontró el componente del checkout');
        $this->assertStringContainsString('tipo_documento', $principal, 'El cuerpo del pedido no incluye el tipo de documento');
        $this->assertStringContainsString('alert(detalle)', $principal, 'El x-data está truncado antes del manejo de errores');
    }

    public function test_el_x_data_del_panel_tambien(): void
    {
        $admin = User::create([
            'nombre' => 'Administrador', 'email' => 'admin-'.uniqid().'@test.cl',
            'password' => Hash::make('secreto12'), 'rol' => 'admin', 'activo' => true,
        ]);

        foreach (['/admin', '/admin/productos/crear', '/admin/documentos'] as $ruta) {
            $html = $this->actingAs($admin)->get($ruta)->assertOk()->getContent();

            foreach ($this->atributosXData($html) as $indice => $expresion) {
                $this->assertSame(
                    substr_count($expresion, '{'),
                    substr_count($expresion, '}'),
                    "El x-data #{$indice} de {$ruta} está truncado."
                );
            }
        }
    }

    public static function rutasPublicas(): array
    {
        return [
            ['/'],
            ['/productos'],
            ['/carrito'],
            ['/checkout'],
            ['/empresas'],
            ['/contacto'],
        ];
    }

    /** @return array<int, string> contenido de cada atributo x-data del HTML */
    private function atributosXData(string $html): array
    {
        // Los atributos van entre comillas dobles, así que el primer " cierra.
        preg_match_all('/x-data="([^"]*)"/', $html, $coincidencias);

        return $coincidencias[1];
    }

    private function prepararCatalogo(): void
    {
        $categoria = Category::firstOrCreate(
            ['slug' => 'agua'],
            ['nombre' => 'Agua', 'orden' => 1, 'activo' => true]
        );

        $producto = Product::create([
            'category_id' => $categoria->id, 'nombre' => 'Bidón 20L', 'slug' => 'bidon-20l',
            'precio' => 3990, 'stock' => 50, 'stock_minimo' => 5, 'activo' => true,
        ]);

        CartItem::create(['session_id' => 'prueba', 'product_id' => $producto->id, 'cantidad' => 2]);
    }
}
