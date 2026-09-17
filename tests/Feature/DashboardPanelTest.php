<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * El dashboard se rediseñó a partir de una maqueta que venía con cifras
 * inventadas. Estas pruebas fijan que lo que se muestra sale de la base: si
 * alguien vuelve a poner un número a mano, la prueba lo delata.
 */
class DashboardPanelTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'nombre'   => 'Administrador',
            'email'    => 'admin-'.uniqid().'@test.cl',
            'password' => Hash::make('secreto12'),
            'rol'      => 'admin',
            'activo'   => true,
        ]);
    }

    private function producto(array $extra = []): Product
    {
        $categoria = Category::firstOrCreate(
            ['slug' => 'agua'],
            ['nombre' => 'Agua', 'orden' => 1, 'activo' => true]
        );

        return Product::create(array_merge([
            'category_id'   => $categoria->id,
            'nombre'        => 'Bidón de prueba '.uniqid(),
            'slug'          => 'bidon-'.uniqid(),
            'precio'        => 3000,
            'stock'         => 50,
            'stock_minimo'  => 5,
            'activo'        => true,
        ], $extra));
    }

    private function pedido(string $estado, float $total, ?string $cuando = null): Order
    {
        $pedido = Order::create([
            'nombre_cliente' => 'Cliente Prueba',
            'email_cliente'  => 'cliente@test.cl',
            'estado'         => $estado,
            'metodo_pago'    => 'webpay',
            'subtotal'       => $total,
            'total'          => $total,
        ]);

        if ($cuando) {
            $pedido->forceFill(['created_at' => $cuando, 'updated_at' => $cuando])->save();
        }

        return $pedido->refresh();
    }

    public function test_el_dashboard_carga_con_la_base_vacia(): void
    {
        // El caso real de hoy: casi no hay datos. No debe reventar ni mostrar
        // divisiones por cero.
        $respuesta = $this->actingAs($this->admin())->get('/admin');

        $respuesta->assertOk()
            ->assertSee('Panel de Control General')
            ->assertSee('Facturación hoy')
            ->assertSee('Pedidos pendientes')
            ->assertSee('Unidades en ruta')
            ->assertSee('Nivel de inventario')
            ->assertSee('Sin pedidos registrados todavía');
    }

    public function test_la_facturacion_del_dia_sale_de_los_pedidos_del_dia(): void
    {
        $this->pedido('entregado', 12000, now()->startOfDay()->addHours(10)->toDateTimeString());
        $this->pedido('confirmado', 8000, now()->startOfDay()->addHours(12)->toDateTimeString());
        // Los cancelados y los pendientes no son venta.
        $this->pedido('cancelado', 99000, now()->startOfDay()->addHours(13)->toDateTimeString());
        $this->pedido('pendiente', 55000, now()->startOfDay()->addHours(14)->toDateTimeString());

        $this->actingAs($this->admin())->get('/admin')
            ->assertOk()
            ->assertSee('$20.000')
            ->assertDontSee('$119.000');
    }

    public function test_las_unidades_en_ruta_cuentan_los_items_de_los_pedidos_enviados(): void
    {
        $producto = $this->producto();

        $enRuta = $this->pedido('enviado', 9000);
        OrderItem::create([
            'order_id' => $enRuta->id, 'product_id' => $producto->id,
            'nombre_producto' => $producto->nombre, 'precio_unitario' => 3000,
            'cantidad' => 7, 'subtotal' => 21000,
        ]);

        // Un pedido entregado ya no va en la calle: no debe sumar.
        $entregado = $this->pedido('entregado', 9000);
        OrderItem::create([
            'order_id' => $entregado->id, 'product_id' => $producto->id,
            'nombre_producto' => $producto->nombre, 'precio_unitario' => 3000,
            'cantidad' => 99, 'subtotal' => 297000,
        ]);

        $respuesta = $this->actingAs($this->admin())->get('/admin');

        $respuesta->assertOk()->assertSee('Unidades en ruta');
        $this->assertSame(7, $respuesta->viewData('stats')['unidades_en_ruta']);
        $this->assertSame(1, $respuesta->viewData('stats')['pedidos_en_ruta']);
    }

    public function test_el_nivel_de_inventario_nombra_los_productos_bajo_el_minimo(): void
    {
        $this->producto(['nombre' => 'Botellón 20L crítico', 'stock' => 1, 'stock_minimo' => 10]);
        $this->producto(['nombre' => 'Producto con stock sano', 'stock' => 80, 'stock_minimo' => 5]);

        $this->actingAs($this->admin())->get('/admin')
            ->assertOk()
            ->assertSee('Stock crítico')
            ->assertSee('Botellón 20L crítico');
    }

    public function test_el_ticket_promedio_es_ventas_del_mes_sobre_pedidos_del_mes(): void
    {
        $this->pedido('entregado', 10000);
        $this->pedido('entregado', 20000);

        $stats = $this->actingAs($this->admin())->get('/admin')->viewData('stats');

        $this->assertSame(30000.0, (float) $stats['ventas_mes']);
        $this->assertSame(2, $stats['pedidos_mes']);
        $this->assertSame(15000.0, (float) $stats['ticket_promedio']);
    }

    public function test_las_tres_series_llegan_completas_a_la_vista(): void
    {
        $respuesta = $this->actingAs($this->admin())->get('/admin')->assertOk();

        foreach (['serieVentas', 'seriePedidos', 'serieClientes'] as $serie) {
            $this->assertCount(30, $respuesta->viewData($serie), "{$serie} debería traer 30 días");
        }

        foreach (['serieAnual', 'serieAnualPedidos', 'serieAnualClientes', 'etiquetasMeses'] as $serie) {
            $this->assertCount(12, $respuesta->viewData($serie), "{$serie} debería traer 12 meses");
        }
    }

    public function test_el_reporte_de_pedidos_se_descarga_como_csv(): void
    {
        $this->pedido('entregado', 45990);

        $respuesta = $this->actingAs($this->admin())->get(route('admin.reportes.pedidos'));

        $respuesta->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $csv = $respuesta->streamedContent();

        // BOM para que Excel en Windows no rompa los acentos.
        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);
        $this->assertStringContainsString('Medio de pago', $csv);
        $this->assertStringContainsString('Cliente Prueba', $csv);
        $this->assertStringContainsString('45990', $csv);
    }

    public function test_el_reporte_no_es_publico(): void
    {
        $this->get(route('admin.reportes.pedidos'))->assertRedirect('/admin/acceso');
    }
}
