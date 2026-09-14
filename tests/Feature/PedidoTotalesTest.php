<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cubre el camino que mueve dinero. Existe porque hubo un periodo en que el
 * checkout mostraba un total y el backend cobraba otro: el umbral de despacho
 * gratis estaba en $15.000 en el frontend y en $30.000 al momento de cobrar.
 */
class PedidoTotalesTest extends TestCase
{
    use RefreshDatabase;

    private function producto(int $precio, int $stock = 100): Product
    {
        $categoria = Category::create(['nombre' => 'Agua', 'slug' => 'agua-'.uniqid()]);

        return Product::create([
            'category_id' => $categoria->id,
            'nombre'      => 'Bidón de prueba',
            'slug'        => 'bidon-'.uniqid(),
            'precio'      => $precio,
            'stock'       => $stock,
            'activo'      => true,
        ]);
    }

    private function crearPedido(Product $producto, int $cantidad, array $extra = []): array
    {
        $sesion = (string) \Illuminate\Support\Str::uuid();

        CartItem::create([
            'session_id' => $sesion,
            'product_id' => $producto->id,
            'cantidad'   => $cantidad,
        ]);

        $respuesta = $this->postJson('/api/orders', array_merge([
            'session_id'     => $sesion,
            'nombre_cliente' => 'Cliente Prueba',
            'email_cliente'  => 'prueba@test.cl',
            'metodo_pago'    => 'webpay',
        ], $extra));

        $respuesta->assertCreated();

        return $respuesta->json();
    }

    public function test_sobre_el_umbral_el_despacho_no_se_cobra(): void
    {
        $umbral = (int) app(\App\Support\Settings::class)->get('despacho_gratis');
        $producto = $this->producto(5000);

        // 4 × 5.000 = 20.000, por encima del umbral de 15.000
        $pedido = $this->crearPedido($producto, 4);

        $this->assertSame(20000.0, (float) $pedido['subtotal']);
        $this->assertGreaterThanOrEqual($umbral, (float) $pedido['subtotal']);
        $this->assertSame(0.0, (float) $pedido['costo_despacho'], 'Sobre el umbral el despacho debe ser gratis');
        $this->assertSame(20000.0, (float) $pedido['total']);
    }

    public function test_bajo_el_umbral_se_cobra_la_tarifa_estandar(): void
    {
        $tarifa = (int) app(\App\Support\Settings::class)->get('despacho_estandar');
        $producto = $this->producto(3000);

        $pedido = $this->crearPedido($producto, 2); // 6.000

        $this->assertSame(6000.0, (float) $pedido['subtotal']);
        $this->assertSame((float) $tarifa, (float) $pedido['costo_despacho']);
        $this->assertSame(6000.0 + $tarifa, (float) $pedido['total']);
    }

    public function test_el_despacho_express_se_respeta_y_no_se_ignora(): void
    {
        $express = (int) app(\App\Support\Settings::class)->get('despacho_express');
        $producto = $this->producto(3000);

        $pedido = $this->crearPedido($producto, 2, ['envio' => 'express']);

        $this->assertSame((float) $express, (float) $pedido['costo_despacho'],
            'La opción express debe llegar al backend, no quedar en el frontend');
    }

    public function test_el_cupon_de_porcentaje_descuenta_sobre_el_subtotal(): void
    {
        Coupon::create([
            'codigo'        => 'PRUEBA10',
            'tipo'          => 'porcentaje',
            'descuento'     => 10,
            'minimo_compra' => 0,
            'activo'        => true,
        ]);

        $producto = $this->producto(5000);
        $pedido = $this->crearPedido($producto, 4, ['cupon' => 'PRUEBA10']); // 20.000

        $this->assertSame(2000.0, (float) $pedido['descuento']);
        $this->assertSame(18000.0, (float) $pedido['total']);
    }

    public function test_el_cupon_de_monto_fijo_respeta_el_minimo_de_compra(): void
    {
        Coupon::create([
            'codigo'        => 'MINIMO',
            'tipo'          => 'monto',
            'descuento'     => 1500,
            'minimo_compra' => 10000,
            'activo'        => true,
        ]);

        $producto = $this->producto(2000);
        $pedido = $this->crearPedido($producto, 2, ['cupon' => 'MINIMO']); // 4.000, bajo el mínimo

        $this->assertSame(0.0, (float) $pedido['descuento'],
            'Un cupón con mínimo de compra no debe aplicarse bajo ese monto');
    }

    public function test_no_se_puede_pedir_mas_stock_del_disponible(): void
    {
        $producto = $this->producto(3000, stock: 2);
        $sesion   = (string) \Illuminate\Support\Str::uuid();

        CartItem::create([
            'session_id' => $sesion,
            'product_id' => $producto->id,
            'cantidad'   => 5,
        ]);

        $this->postJson('/api/orders', [
            'session_id'     => $sesion,
            'nombre_cliente' => 'Cliente Prueba',
            'email_cliente'  => 'prueba@test.cl',
            'metodo_pago'    => 'webpay',
        ])->assertStatus(422);

        $this->assertSame(2, $producto->fresh()->stock, 'Un pedido rechazado no debe descontar stock');
    }

    public function test_el_pedido_descuenta_stock_y_vacia_el_carrito(): void
    {
        $producto = $this->producto(3000, stock: 10);
        $sesion   = (string) \Illuminate\Support\Str::uuid();

        CartItem::create([
            'session_id' => $sesion,
            'product_id' => $producto->id,
            'cantidad'   => 3,
        ]);

        $this->postJson('/api/orders', [
            'session_id'     => $sesion,
            'nombre_cliente' => 'Cliente Prueba',
            'email_cliente'  => 'prueba@test.cl',
            'metodo_pago'    => 'webpay',
        ])->assertCreated();

        $this->assertSame(7, $producto->fresh()->stock);
        $this->assertDatabaseMissing('cart_items', ['session_id' => $sesion]);
    }
}
