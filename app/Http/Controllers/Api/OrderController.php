<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmationMail;
use App\Rules\Rut;
use App\Services\Tributario\EmisorDocumentos;
use App\Support\Iva;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /** POST /api/orders */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id'    => 'required|string',
            'nombre_cliente'=> 'required|string|max:255',
            'email_cliente' => 'required|email|max:255',
            'telefono'      => 'nullable|string|max:20',
            'direccion'     => 'nullable|string|max:500',
            'comuna'        => 'nullable|string|max:100',
            'ciudad'        => 'nullable|string|max:100',
            'metodo_pago'   => 'required|in:webpay,mercadopago,transferencia,whatsapp,contra_entrega',
            'notas'         => 'nullable|string|max:1000',
            'cupon'         => 'nullable|string|max:50',
            'envio'         => 'nullable|in:standard,express',

            // Documento tributario. Boleta por omisión: es lo que corresponde a
            // una persona. La factura exige los datos del receptor, y se piden
            // todos porque un dato faltante obliga a anular y reemitir.
            'tipo_documento'    => 'nullable|in:boleta,factura',
            'rut_receptor'      => ['nullable', 'required_if:tipo_documento,factura', 'string', 'max:20', new Rut],
            'razon_social'      => 'nullable|required_if:tipo_documento,factura|string|max:255',
            'giro'              => 'nullable|required_if:tipo_documento,factura|string|max:255',
            'direccion_factura' => 'nullable|required_if:tipo_documento,factura|string|max:500',
            'comuna_factura'    => 'nullable|required_if:tipo_documento,factura|string|max:100',
        ], [], [
            'rut_receptor'      => 'RUT',
            'razon_social'      => 'razón social',
            'direccion_factura' => 'dirección de facturación',
            'comuna_factura'    => 'comuna de facturación',
        ]);

        $cartItems = CartItem::with('product')
            ->where('session_id', $validated['session_id'])
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'El carrito está vacío.'], 422);
        }

        // Verificar stock
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->cantidad) {
                return response()->json([
                    'message' => "Stock insuficiente para «{$item->product->nombre}». Disponibles: {$item->product->stock}",
                ], 422);
            }
        }

        $order = DB::transaction(function () use ($validated, $cartItems) {
            $subtotal = $cartItems->sum(fn ($i) => $i->product->precio * $i->cantidad);

            $tarifas  = app(\App\Support\Settings::class)->publicos()['despacho'];
            $express  = ($validated['envio'] ?? 'standard') === 'express';
            $despacho = $subtotal >= $tarifas['gratis_desde']
                ? 0
                : ($express ? $tarifas['express'] : $tarifas['estandar']);

            $descuento = 0;

            // Validar cupón si existe
            if (! empty($validated['cupon'])) {
                $coupon = \App\Models\Coupon::where('codigo', $validated['cupon'])->first();
                if ($coupon && $coupon->esValido()) {
                    $descuento = $coupon->calcularDescuento($subtotal);
                    $coupon->increment('usos_actuales');
                }
            }

            $total = $subtotal + $despacho - $descuento;

            // Los precios del catálogo ya incluyen IVA, así que el neto y el
            // IVA se desglosan del total cobrado. Se guardan en el pedido para
            // que el documento no dependa de recalcularlos más adelante.
            $desglose = Iva::desglosar($total);

            $esFactura = ($validated['tipo_documento'] ?? 'boleta') === 'factura';

            $order = Order::create([
                'user_id'        => auth()->id(),
                'nombre_cliente' => $validated['nombre_cliente'],
                'email_cliente'  => $validated['email_cliente'],
                'telefono'       => $validated['telefono'] ?? null,
                'direccion'      => $validated['direccion'] ?? null,
                'comuna'         => $validated['comuna'] ?? null,
                'ciudad'         => $validated['ciudad'] ?? null,
                'metodo_pago'    => $validated['metodo_pago'],
                'notas'          => $validated['notas'] ?? null,

                'tipo_documento'    => $esFactura ? 'factura' : 'boleta',
                'rut_receptor'      => $esFactura ? Rut::formatear($validated['rut_receptor']) : null,
                'razon_social'      => $esFactura ? $validated['razon_social'] : null,
                'giro'              => $esFactura ? $validated['giro'] : null,
                'direccion_factura' => $esFactura ? $validated['direccion_factura'] : null,
                'comuna_factura'    => $esFactura ? $validated['comuna_factura'] : null,

                'subtotal'       => $subtotal,
                'neto'           => $desglose['neto'],
                'iva'            => $desglose['iva'],
                'costo_despacho' => $despacho,
                'descuento'      => $descuento,
                'total'          => $total,
                'estado'         => 'pendiente',
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'        => $order->id,
                    'product_id'      => $item->product_id,
                    'nombre_producto' => $item->product->nombre,
                    'precio_unitario' => $item->product->precio,
                    'cantidad'        => $item->cantidad,
                    'subtotal'        => $item->product->precio * $item->cantidad,
                ]);

                // Descontar stock
                $item->product->decrement('stock', $item->cantidad);
            }

            // Limpiar carrito
            CartItem::where('session_id', $validated['session_id'])->delete();

            return $order;
        });

        // El documento se registra fuera de la transacción: con un emisor que
        // llame a una API externa, esa llamada no puede vivir dentro de una
        // transacción abierta. Si falla, el pedido igual queda: el panel lista
        // los pedidos sin documento para no perderlos de vista.
        try {
            app(EmisorDocumentos::class)->emitir($order);
        } catch (\Throwable $e) {
            report($e);
        }

        // Enviar email de confirmación (silencioso si falla)
        try {
            Mail::to($order->email_cliente)->send(new OrderConfirmationMail($order->load('items')));
        } catch (\Throwable) {}

        return response()->json($order->load('items'), 201);
    }

    /** GET /api/orders/{token} */
    public function show(string $token): JsonResponse
    {
        // Igual que la vista de confirmación: sólo se llega con el token del pedido.
        $order = Order::with('items.product')->where('token', $token)->firstOrFail();

        return response()->json($order);
    }
}
