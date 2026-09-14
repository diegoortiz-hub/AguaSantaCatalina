<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Humo básico: que ninguna página de la tienda devuelva error. Ya hubo un caso
 * en que /nosotros reventaba con 500 por una ruta con nombre inexistente y nadie
 * lo notó hasta revisarlo a mano.
 */
class RutasPublicasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $categoria = Category::create(['nombre' => 'Agua Purificada', 'slug' => 'agua-purificada']);

        Product::create([
            'category_id' => $categoria->id,
            'nombre'      => 'Bidón 20L',
            'slug'        => 'bidon-20l',
            'precio'      => 3990,
            'stock'       => 50,
            'destacado'   => true,
            'activo'      => true,
        ]);
    }

    public static function rutas(): array
    {
        return [
            'inicio'    => ['/'],
            'catálogo'  => ['/productos'],
            'ofertas'   => ['/ofertas'],
            'empresas'  => ['/empresas'],
            'nosotros'  => ['/nosotros'],
            'contacto'  => ['/contacto'],
            'carrito'   => ['/carrito'],
            'checkout'  => ['/checkout'],
            'login'     => ['/login'],
            'recuperar' => ['/olvide-mi-clave'],
        ];
    }

    #[DataProvider('rutas')]
    public function test_la_pagina_responde_sin_error(string $ruta): void
    {
        $this->get($ruta)->assertOk();
    }

    public function test_la_ficha_de_producto_responde(): void
    {
        $this->get('/productos/bidon-20l')->assertOk()->assertSee('Bidón 20L', false);
    }

    public function test_un_producto_inexistente_da_404(): void
    {
        $this->get('/productos/no-existe')->assertNotFound();
    }
}
