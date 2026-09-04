<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{
    /** POST /api/coupons/validate */
    public function validate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo'   => 'required|string|max:50',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('codigo', strtoupper($validated['codigo']))->first();

        if (! $coupon || ! $coupon->esValido()) {
            return response()->json([
                'valido'   => false,
                'message'  => 'Cupón inválido, vencido o sin usos disponibles.',
            ], 422);
        }

        if ($validated['subtotal'] < $coupon->minimo_compra) {
            return response()->json([
                'valido'   => false,
                'message'  => "El cupón requiere un mínimo de $" . number_format($coupon->minimo_compra, 0, ',', '.'),
            ], 422);
        }

        $descuento = $coupon->calcularDescuento((float) $validated['subtotal']);

        return response()->json([
            'valido'    => true,
            'codigo'    => $coupon->codigo,
            'tipo'      => $coupon->tipo,
            'descuento' => $descuento,
            'message'   => "Cupón aplicado: -$" . number_format($descuento, 0, ',', '.'),
        ]);
    }
}
