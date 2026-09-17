<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\DocumentoTributario;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Rules\Rut;
use App\Support\Iva;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Boletas y facturas.
 *
 * Lo que se fija acá: que el desglose del IVA cuadre al peso con lo cobrado,
 * que una factura no se pueda pedir sin los datos del receptor, y que el libro
 * de ventas sólo cuente lo que se emitió de verdad.
 */
class DocumentosTributariosTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'nombre' => 'Administrador', 'email' => 'admin-'.uniqid().'@test.cl',
            'password' => Hash::make('secreto12'), 'rol' => 'admin', 'activo' => true,
        ]);
    }

    private function producto(int $precio = 3990): Product
    {
        $categoria = Category::firstOrCreate(
            ['slug' => 'agua'],
            ['nombre' => 'Agua', 'orden' => 1, 'activo' => true]
        );

        return Product::create([
            'category_id'  => $categoria->id,
            'nombre'       => 'Bidón 20L',
            'slug'         => 'bidon-20l-'.uniqid(),
            'precio'       => $precio,
            'stock'        => 100,
            'stock_minimo' => 5,
            'activo'       => true,
        ]);
    }

    /** Deja un carrito listo y devuelve el session_id. */
    private function carrito(int $cantidad = 5, int $precio = 3990): string
    {
        $sesion = 'sesion-'.uniqid();

        CartItem::create([
            'session_id' => $sesion,
            'product_id' => $this->producto($precio)->id,
            'cantidad'   => $cantidad,
        ]);

        return $sesion;
    }

    private function datosBase(string $sesion): array
    {
        return [
            'session_id'     => $sesion,
            'nombre_cliente' => 'María González',
            'email_cliente'  => 'maria@ejemplo.cl',
            'direccion'      => 'Av. Providencia 1234',
            'comuna'         => 'Providencia',
            'metodo_pago'    => 'transferencia',
        ];
    }

    // ── Desglose del IVA ────────────────────────────────────────────────

    #[DataProvider('montos')]
    public function test_el_neto_y_el_iva_suman_exactamente_el_total(int $bruto): void
    {
        $d = Iva::desglosar($bruto);

        $this->assertSame($bruto, $d['neto'] + $d['iva'], "El desglose de {$bruto} no cuadra");
        $this->assertSame($bruto, $d['total']);
    }

    public static function montos(): array
    {
        // Incluye montos con resto feo a propósito: si el IVA se calculara
        // multiplicando el neto otra vez, varios de estos quedarían un peso
        // arriba o abajo del total cobrado.
        return [[1], [3990], [7180], [13990], [19950], [22440], [45990], [100000], [123457]];
    }

    public function test_el_desglose_de_cero_no_revienta(): void
    {
        $this->assertSame(['neto' => 0, 'iva' => 0, 'total' => 0], Iva::desglosar(0));
    }

    // ── RUT ─────────────────────────────────────────────────────────────

    #[DataProvider('rutsValidos')]
    public function test_acepta_ruts_validos(string $rut): void
    {
        $this->assertTrue($this->rutPasa($rut), "{$rut} debería ser válido");
    }

    public static function rutsValidos(): array
    {
        return [['11.111.111-1'], ['12.345.678-5'], ['76.086.428-5'], ['5.126.663-3'], ['12345670K']];
    }

    #[DataProvider('rutsInvalidos')]
    public function test_rechaza_ruts_invalidos(string $rut): void
    {
        $this->assertFalse($this->rutPasa($rut), "{$rut} no debería pasar");
    }

    public static function rutsInvalidos(): array
    {
        // El último es el caso que importa: formato correcto, dígito cambiado.
        return [[''], ['12345'], ['no-es-un-rut'], ['12.345.678-9']];
    }

    private function rutPasa(string $rut): bool
    {
        $paso = true;
        (new Rut)->validate('rut', $rut, function () use (&$paso) { $paso = false; });

        return $paso;
    }

    // ── Compra con boleta ───────────────────────────────────────────────

    public function test_una_compra_normal_queda_con_boleta_y_su_desglose(): void
    {
        $sesion = $this->carrito(cantidad: 5, precio: 3990);

        $respuesta = $this->postJson('/api/orders', $this->datosBase($sesion));
        $respuesta->assertCreated();

        $pedido = Order::firstOrFail();

        $this->assertSame('boleta', $pedido->tipo_documento);
        $this->assertNull($pedido->rut_receptor);

        // 5 × 3990 = 19.950, bajo el mínimo de despacho gratis, así que suma flete.
        $desglose = Iva::desglosar((float) $pedido->total);
        $this->assertSame((float) $desglose['neto'], (float) $pedido->neto);
        $this->assertSame((float) $desglose['iva'], (float) $pedido->iva);
        $this->assertSame((float) $pedido->total, (float) $pedido->neto + (float) $pedido->iva);

        $documento = $pedido->documentos()->firstOrFail();
        $this->assertSame('boleta', $documento->tipo);
        $this->assertSame('pendiente', $documento->estado);
        $this->assertNull($documento->folio);
        $this->assertSame((float) $pedido->total, (float) $documento->total);
    }

    // ── Compra con factura ──────────────────────────────────────────────

    public function test_una_factura_sin_datos_del_receptor_se_rechaza(): void
    {
        $sesion = $this->carrito();

        $this->postJson('/api/orders', $this->datosBase($sesion) + ['tipo_documento' => 'factura'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['rut_receptor', 'razon_social', 'giro', 'direccion_factura', 'comuna_factura']);

        $this->assertSame(0, Order::count());
    }

    public function test_una_factura_con_rut_mal_tipeado_se_rechaza(): void
    {
        $sesion = $this->carrito();

        $this->postJson('/api/orders', $this->datosBase($sesion) + [
            'tipo_documento'    => 'factura',
            'rut_receptor'      => '12.345.678-9',
            'razon_social'      => 'Comercial Ejemplo SpA',
            'giro'              => 'Venta de alimentos',
            'direccion_factura' => 'Av. Apoquindo 4500',
            'comuna_factura'    => 'Las Condes',
        ])->assertStatus(422)->assertJsonValidationErrors(['rut_receptor']);

        $this->assertSame(0, Order::count());
    }

    public function test_una_compra_de_empresa_queda_con_factura_y_el_rut_normalizado(): void
    {
        $sesion = $this->carrito(cantidad: 10);

        $this->postJson('/api/orders', $this->datosBase($sesion) + [
            'tipo_documento'    => 'factura',
            'rut_receptor'      => '760864285',
            'razon_social'      => 'Comercial Ejemplo SpA',
            'giro'              => 'Venta de alimentos',
            'direccion_factura' => 'Av. Apoquindo 4500, Of. 302',
            'comuna_factura'    => 'Las Condes',
        ])->assertCreated();

        $pedido = Order::firstOrFail();

        $this->assertSame('factura', $pedido->tipo_documento);
        $this->assertSame('76.086.428-5', $pedido->rut_receptor, 'El RUT debería quedar formateado');
        $this->assertSame('Comercial Ejemplo SpA', $pedido->razon_social);
        $this->assertSame('factura', $pedido->documentos()->firstOrFail()->tipo);
    }

    // ── Panel ───────────────────────────────────────────────────────────

    public function test_registrar_el_folio_marca_emitido_y_avisa_al_cliente(): void
    {
        Mail::fake();

        $sesion = $this->carrito();
        $this->postJson('/api/orders', $this->datosBase($sesion))->assertCreated();
        $documento = DocumentoTributario::firstOrFail();

        $this->actingAs($this->admin())
            ->put(route('admin.documentos.update', $documento), [
                'tipo'          => 'boleta',
                'folio'         => 1042,
                'fecha_emision' => now()->toDateString(),
                'avisar'        => 1,
            ])
            ->assertRedirect(route('admin.documentos.show', $documento));

        $documento->refresh();
        $this->assertSame('emitido', $documento->estado);
        $this->assertSame(1042, $documento->folio);

        Mail::assertSent(\App\Mail\DocumentoTributarioMail::class);
    }

    public function test_no_se_puede_repetir_el_folio_dentro_del_mismo_tipo(): void
    {
        $primero = $this->documentoEmitido(folio: 500);

        $segundo = DocumentoTributario::create([
            'order_id' => $primero->order_id, 'tipo' => 'boleta', 'total' => 1000, 'estado' => 'pendiente',
        ]);

        $this->actingAs($this->admin())
            ->from(route('admin.documentos.show', $segundo))
            ->put(route('admin.documentos.update', $segundo), [
                'tipo'          => 'boleta',
                'folio'         => 500,
                'fecha_emision' => now()->toDateString(),
            ])
            ->assertSessionHasErrors('folio');

        $this->assertSame('pendiente', $segundo->refresh()->estado);
    }

    public function test_anular_conserva_el_registro_con_el_motivo(): void
    {
        $documento = $this->documentoEmitido(folio: 900);

        $this->actingAs($this->admin())
            ->post(route('admin.documentos.anular', $documento), ['motivo' => 'RUT mal tipeado por el cliente']);

        $documento->refresh();
        $this->assertSame('anulado', $documento->estado);
        $this->assertNotNull($documento->anulado_at);
        $this->assertStringContainsString('RUT mal tipeado', $documento->observaciones);
        $this->assertDatabaseCount('documentos_tributarios', 1);
    }

    public function test_el_libro_de_ventas_solo_trae_lo_emitido(): void
    {
        $emitido = $this->documentoEmitido(folio: 1);
        $anulado = $this->documentoEmitido(folio: 2);
        $anulado->update(['estado' => 'anulado']);
        DocumentoTributario::create([
            'order_id' => $emitido->order_id, 'tipo' => 'boleta', 'total' => 5000, 'estado' => 'pendiente',
        ]);

        $respuesta = $this->actingAs($this->admin())
            ->get(route('admin.documentos.libro', ['mes' => now()->format('Y-m')]));

        $respuesta->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $csv = $respuesta->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);

        // Cabecera + sólo el documento emitido.
        $filas = array_filter(explode("\n", trim($csv)));
        $this->assertCount(2, $filas, 'Sólo debería salir el documento emitido');
        $this->assertStringContainsString('Boleta electrónica', $csv);
    }

    public function test_el_modulo_no_es_publico(): void
    {
        $this->get(route('admin.documentos.index'))->assertRedirect('/admin/acceso');
        $this->get(route('admin.documentos.libro'))->assertRedirect('/admin/acceso');
    }

    public function test_el_panel_lista_los_pedidos_sin_documento(): void
    {
        // Un pedido creado a mano, sin pasar por el emisor.
        Order::create([
            'nombre_cliente' => 'Pedido Huérfano', 'email_cliente' => 'h@test.cl',
            'estado' => 'pendiente', 'metodo_pago' => 'webpay', 'subtotal' => 5000, 'total' => 5000,
        ]);

        $this->actingAs($this->admin())->get(route('admin.documentos.index'))
            ->assertOk()
            ->assertSee('sin documento registrado')
            ->assertSee('Pedido Huérfano');
    }

    private function documentoEmitido(int $folio): DocumentoTributario
    {
        $pedido = Order::create([
            'nombre_cliente' => 'Cliente Prueba', 'email_cliente' => 'c@test.cl',
            'estado' => 'entregado', 'metodo_pago' => 'webpay',
            'subtotal' => 10000, 'total' => 10000, 'neto' => 8403, 'iva' => 1597,
        ]);

        return $pedido->documentos()->create([
            'tipo'          => 'boleta',
            'folio'         => $folio,
            'fecha_emision' => now()->toDateString(),
            'neto'          => 8403,
            'iva'           => 1597,
            'total'         => 10000,
            'estado'        => 'emitido',
            'emisor'        => 'manual',
        ]);
    }
}
