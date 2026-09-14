<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Los pedidos se consultaban por id correlativo y sin autenticación, así que
 * recorrer 1, 2, 3... entregaba nombre, correo, teléfono y dirección de cada
 * compra. Ahora se identifican por token.
 */
class PrivacidadPedidoTest extends TestCase
{
    use RefreshDatabase;

    private function pedido(): Order
    {
        return Order::create([
            'nombre_cliente' => 'Francisca Silva',
            'email_cliente'  => 'francisca@test.cl',
            'telefono'       => '+56 9 8452 1190',
            'direccion'      => 'Av. Irarrázaval 2401',
            'comuna'         => 'Ñuñoa',
            'estado'         => 'confirmado',
            'metodo_pago'    => 'webpay',
            'subtotal'       => 21940,
            'total'          => 21940,
        ]);
    }

    public function test_cada_pedido_nace_con_su_token(): void
    {
        $pedido = $this->pedido();

        $this->assertNotEmpty($pedido->token);
        $this->assertNotSame((string) $pedido->id, $pedido->token);
    }

    public function test_el_id_correlativo_no_sirve_para_ver_un_pedido(): void
    {
        $pedido = $this->pedido();

        $this->get("/pedido/{$pedido->id}/confirmacion")->assertNotFound();
        $this->getJson("/api/orders/{$pedido->id}")->assertNotFound();
    }

    public function test_el_token_da_acceso_a_la_confirmacion(): void
    {
        $pedido = $this->pedido();

        $this->get("/pedido/{$pedido->token}/confirmacion")
            ->assertOk()
            // El correo es justamente el dato que quedaba expuesto al recorrer ids.
            ->assertSee('francisca@test.cl', false);
    }

    public function test_un_token_inventado_no_entrega_nada(): void
    {
        $this->pedido();

        $inventado = '11111111-2222-3333-4444-555555555555';

        $this->get("/pedido/{$inventado}/confirmacion")->assertNotFound();
        $this->getJson("/api/orders/{$inventado}")->assertNotFound();
    }

    public function test_el_token_no_se_puede_fijar_desde_una_peticion(): void
    {
        // Si 'token' fuera asignable en masa, quien crea un pedido podría elegir
        // un token ya existente y quedar con acceso a un pedido ajeno.
        $elegido = '99999999-8888-7777-6666-555555555555';

        $pedido = new Order();
        $pedido->fill(['token' => $elegido, 'nombre_cliente' => 'Intruso', 'email_cliente' => 'x@test.cl']);
        $pedido->save();

        $this->assertNotSame($elegido, $pedido->token);
    }
}
