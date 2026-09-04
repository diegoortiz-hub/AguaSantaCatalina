@extends('layouts.app')

@section('title', 'Carrito de Compras')

@section('content')
<h1 style="margin-bottom:1.5rem;">🛒 Carrito de Compras</h1>
<div id="cart-content">
    <p style="color:#888;">Cargando carrito...</p>
</div>
@endsection

@push('scripts')
<script>
const sessionId = localStorage.getItem('cart_session');

async function loadCart() {
    if (!sessionId) { document.getElementById('cart-content').innerHTML = '<p>Tu carrito está vacío.</p>'; return; }
    const res = await fetch(`/api/cart/${sessionId}`);
    const data = await res.json();

    if (!data.items || data.items.length === 0) {
        document.getElementById('cart-content').innerHTML = '<p style="color:#888;">Tu carrito está vacío. <a href="/productos">Ver productos</a></p>';
        return;
    }

    let html = '<table style="width:100%;border-collapse:collapse;">';
    html += '<thead><tr style="background:#f5f5f5;"><th style="padding:.8rem;text-align:left;">Producto</th><th>Precio</th><th>Cant.</th><th>Subtotal</th><th></th></tr></thead><tbody>';
    data.items.forEach(item => {
        const sub = item.product.precio * item.cantidad;
        html += `<tr style="border-bottom:1px solid #eee;">
            <td style="padding:.8rem;">${item.product.nombre}</td>
            <td style="padding:.8rem;text-align:center;">$${item.product.precio.toLocaleString('es-CL')}</td>
            <td style="padding:.8rem;text-align:center;">${item.cantidad}</td>
            <td style="padding:.8rem;text-align:center;">$${sub.toLocaleString('es-CL')}</td>
            <td><button onclick="removeItem(${item.product_id})" style="background:#d62828;color:#fff;border:none;padding:.3rem .7rem;border-radius:4px;cursor:pointer;">✕</button></td>
        </tr>`;
    });
    html += `</tbody></table>
    <div style="margin-top:1.5rem;text-align:right;">
        <p style="font-size:1.2rem;font-weight:700;">Total: $${data.subtotal.toLocaleString('es-CL')}</p>
        <a href="/checkout" class="btn btn-primary" style="margin-top:1rem;display:inline-block;">Ir al Checkout →</a>
    </div>`;
    document.getElementById('cart-content').innerHTML = html;
}

async function removeItem(productId) {
    await fetch(`/api/cart/${sessionId}/${productId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content } });
    loadCart();
}

loadCart();
</script>
@endpush
