<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmationMail;
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
            $despacho = $subtotal >= 30000 ? 0 : 2990;
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
                'subtotal'       => $subtotal,
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

        // Enviar email de confirmación (silencioso si falla)
        try {
            Mail::to($order->email_cliente)->send(new OrderConfirmationMail($order->load('items')));
        } catch (\Throwable) {}

        return response()->json($order->load('items'), 201);
    }

    /** GET /api/orders/{id} */
    public function show(int $id): JsonResponse
    {
        $order = Order::with('items.product')->findOrFail($id);

        return response()->json($order);
    }
}
