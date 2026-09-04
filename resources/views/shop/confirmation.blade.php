@extends('layouts.app')

@section('title', 'Pedido Confirmado')

@section('content')
<div style="max-width:700px; margin:2rem auto; text-align:center;">
    <div style="font-size:4rem; margin-bottom:1rem;">✅</div>
    <h1 style="color:#2d6a4f; margin-bottom:.5rem;">¡Pedido recibido!</h1>
    <p style="color:#555; font-size:1.1rem; margin-bottom:2rem;">
        Tu pedido <strong>#{{ $order->id }}</strong> fue registrado correctamente.<br>
        Te contactaremos a <strong>{{ $order->email_cliente }}</strong> para confirmar.
    </p>

    <div style="background:#f9f9f9; border-radius:10px; padding:1.5rem; text-align:left; margin-bottom:2rem;">
        <h3 style="margin-bottom:1rem;">Detalle del pedido</h3>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid #ddd;">
                    <th style="padding:.5rem; text-align:left;">Producto</th>
                    <th style="padding:.5rem; text-align:center;">Cant.</th>
                    <th style="padding:.5rem; text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:.5rem;">{{ $item->nombre_producto }}</td>
                    <td style="padding:.5rem; text-align:center;">{{ $item->cantidad }}</td>
                    <td style="padding:.5rem; text-align:right;">${{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="padding:.5rem; text-align:right;">Despacho:</td>
                    <td style="padding:.5rem; text-align:right;">${{ number_format($order->costo_despacho, 0, ',', '.') }}</td>
                </tr>
                @if($order->descuento > 0)
                <tr style="color:#2d6a4f;">
                    <td colspan="2" style="padding:.5rem; text-align:right;">Descuento:</td>
                    <td style="padding:.5rem; text-align:right;">-${{ number_format($order->descuento, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr style="font-weight:700; font-size:1.1rem; border-top:2px solid #ddd;">
                    <td colspan="2" style="padding:.5rem; text-align:right;">Total:</td>
                    <td style="padding:.5rem; text-align:right; color:#0077b6;">${{ number_format($order->total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div style="margin-top:1rem; display:grid; grid-template-columns:1fr 1fr; gap:.5rem; font-size:.9rem; color:#555;">
            <div><strong>Método de pago:</strong> {{ $order->metodoPagoLabel() }}</div>
            <div><strong>Estado:</strong> <span style="color:#f77f00; font-weight:700;">{{ $order->estadoLabel() }}</span></div>
            @if($order->direccion)
            <div style="grid-column:1/-1"><strong>Despacho a:</strong> {{ $order->direccion }}, {{ $order->comuna }}, {{ $order->ciudad }}</div>
            @endif
        </div>
    </div>

    <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
        <a href="{{ route('home') }}" class="btn btn-primary">← Volver al inicio</a>
        @if($order->metodo_pago === 'whatsapp')
        <a href="https://wa.me/56999999999?text=Hola!%20Mi%20pedido%20es%20el%20%23{{ $order->id }}"
           class="btn btn-whatsapp" target="_blank">💬 Confirmar por WhatsApp</a>
        @endif
    </div>
</div>
@endsection
