<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    /** POST /api/cart */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => 'required|string|max:255',
            'product_id' => 'required|integer|exists:products,id',
            'cantidad'   => 'integer|min:1|max:99',
        ]);

        $product = Product::activo()->findOrFail($validated['product_id']);
        $cantidad = $validated['cantidad'] ?? 1;

        if ($product->stock < $cantidad) {
            return response()->json([
                'message' => "Stock insuficiente. Disponibles: {$product->stock}",
            ], 422);
        }

        $item = CartItem::updateOrCreate(
            [
                'session_id' => $validated['session_id'],
                'product_id' => $product->id,
            ],
            ['cantidad' => $cantidad]
        );

        return response()->json($item->load('product'), 201);
    }

    /** GET /api/cart/{session} */
    public function show(string $session): JsonResponse
    {
        $items = CartItem::with('product.category')
            ->where('session_id', $session)
            ->get();

        $subtotal = $items->sum(fn ($item) => $item->product->precio * $item->cantidad);

        return response()->json([
            'items'    => $items,
            'subtotal' => $subtotal,
            'count'    => $items->sum('cantidad'),
        ]);
    }

    /** DELETE /api/cart/{session}/{product} */
    public function destroy(string $session, int $product): JsonResponse
    {
        $deleted = CartItem::where('session_id', $session)
            ->where('product_id', $product)
            ->delete();

        if (! $deleted) {
            return response()->json(['message' => 'Item no encontrado.'], 404);
        }

        return response()->json(['message' => 'Producto eliminado del carrito.']);
    }
}
