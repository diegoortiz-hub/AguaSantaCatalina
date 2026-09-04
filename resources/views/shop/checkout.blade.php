@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<h1 style="margin-bottom:1.5rem;">Finalizar Compra</h1>

<div style="display:grid; grid-template-columns:1fr 380px; gap:2rem; align-items:start;">
    <!-- Formulario -->
    <form id="checkout-form">
        @csrf
        <fieldset style="border:1px solid #eee; border-radius:8px; padding:1.5rem; margin-bottom:1.5rem;">
            <legend style="font-weight:700; padding:0 .5rem;">Datos de contacto</legend>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-top:1rem;">
                <div>
                    <label>Nombre completo *</label>
                    <input type="text" id="nombre" required placeholder="María González" style="width:100%;padding:.5rem;border:1px solid #ddd;border-radius:6px;margin-top:.3rem;">
                </div>
                <div>
                    <label>Email *</label>
                    <input type="email" id="email" required placeholder="maria@example.cl" style="width:100%;padding:.5rem;border:1px solid #ddd;border-radius:6px;margin-top:.3rem;">
                </div>
                <div>
                    <label>Teléfono</label>
                    <input type="tel" id="telefono" placeholder="+56 9 1234 5678" style="width:100%;padding:.5rem;border:1px solid #ddd;border-radius:6px;margin-top:.3rem;">
                </div>
            </div>
        </fieldset>

        <fieldset style="border:1px solid #eee; border-radius:8px; padding:1.5rem; margin-bottom:1.5rem;">
            <legend style="font-weight:700; padding:0 .5rem;">Dirección de despacho</legend>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-top:1rem;">
                <div style="grid-column:1/-1">
                    <label>Dirección</label>
                    <input type="text" id="direccion" placeholder="Av. Providencia 1234, Dpto 5" style="width:100%;padding:.5rem;border:1px solid #ddd;border-radius:6px;margin-top:.3rem;">
                </div>
                <div>
                    <label>Comuna</label>
                    <input type="text" id="comuna" placeholder="Providencia" style="width:100%;padding:.5rem;border:1px solid #ddd;border-radius:6px;margin-top:.3rem;">
                </div>
                <div>
                    <label>Ciudad</label>
                    <input type="text" id="ciudad" placeholder="Santiago" style="width:100%;padding:.5rem;border:1px solid #ddd;border-radius:6px;margin-top:.3rem;">
                </div>
            </div>
        </fieldset>

        <fieldset style="border:1px solid #eee; border-radius:8px; padding:1.5rem; margin-bottom:1.5rem;">
            <legend style="font-weight:700; padding:0 .5rem;">Método de pago</legend>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-top:1rem;">
                @foreach(['whatsapp'=>'💬 WhatsApp','transferencia'=>'🏦 Transferencia','webpay'=>'💳 Webpay Plus','mercadopago'=>'💰 MercadoPago'] as $val => $label)
                <label style="display:flex; align-items:center; gap:.5rem; padding:.8rem; border:2px solid #ddd; border-radius:8px; cursor:pointer;">
                    <input type="radio" name="metodo_pago" value="{{ $val }}" {{ $val === 'whatsapp' ? 'checked' : '' }}>
                    {{ $label }}
                </label>
                @endforeach
            </div>
        </fieldset>

        <div style="margin-bottom:1.5rem;">
            <label>Cupón de descuento</label>
            <div style="display:flex; gap:.5rem; margin-top:.3rem;">
                <input type="text" id="cupon" placeholder="PROMO10" style="flex:1;padding:.5rem;border:1px solid #ddd;border-radius:6px;">
                <button type="button" onclick="validarCupon()" class="btn btn-primary">Aplicar</button>
            </div>
            <p id="cupon-msg" style="margin-top:.3rem; font-size:.85rem;"></p>
        </div>

        <textarea id="notas" placeholder="Notas del pedido (horario de entrega, instrucciones, etc.)" style="width:100%;padding:.5rem;border:1px solid #ddd;border-radius:6px;height:80px;margin-bottom:1.5rem;"></textarea>

        <button type="submit" class="btn btn-primary" style="width:100%;font-size:1.1rem;padding:.8rem;">Confirmar Pedido →</button>
    </form>

    <!-- Resumen -->
    <div id="resumen" style="border:1px solid #eee; border-radius:8px; padding:1.5rem; position:sticky; top:1rem;">
        <h3 style="margin-bottom:1rem;">Resumen del pedido</h3>
        <div id="resumen-items"><p style="color:#888;">Cargando...</p></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const sessionId = localStorage.getItem('cart_session');
let cuponDescuento = 0;
let subtotalGlobal = 0;

async function loadResumen() {
    if (!sessionId) { window.location = '/carrito'; return; }
    const res = await fetch(`/api/cart/${sessionId}`);
    const data = await res.json();
    subtotalGlobal = data.subtotal || 0;
    const despacho = subtotalGlobal >= 30000 ? 0 : 2990;
    const total = subtotalGlobal + despacho - cuponDescuento;

    let html = data.items.map(i => `<div style="display:flex;justify-content:space-between;margin-bottom:.5rem;font-size:.9rem;">
        <span>${i.product.nombre} × ${i.cantidad}</span>
        <span>$${(i.product.precio * i.cantidad).toLocaleString('es-CL')}</span>
    </div>`).join('');
    html += `<hr style="margin:.8rem 0;">
    <div style="display:flex;justify-content:space-between;"><span>Subtotal</span><span>$${subtotalGlobal.toLocaleString('es-CL')}</span></div>
    <div style="display:flex;justify-content:space-between;"><span>Despacho</span><span>${despacho === 0 ? '<span style="color:green">Gratis</span>' : '$'+despacho.toLocaleString('es-CL')}</span></div>
    ${cuponDescuento > 0 ? `<div style="display:flex;justify-content:space-between;color:green;"><span>Descuento</span><span>-$${cuponDescuento.toLocaleString('es-CL')}</span></div>` : ''}
    <hr style="margin:.8rem 0;">
    <div style="display:flex;justify-content:space-between;font-size:1.2rem;font-weight:700;"><span>Total</span><span>$${total.toLocaleString('es-CL')}</span></div>`;
    document.getElementById('resumen-items').innerHTML = html;
}

async function validarCupon() {
    const codigo = document.getElementById('cupon').value.trim();
    if (!codigo) return;
    const res = await fetch('/api/coupons/validate', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content},
        body: JSON.stringify({codigo, subtotal: subtotalGlobal})
    });
    const data = await res.json();
    const msg = document.getElementById('cupon-msg');
    if (data.valido) {
        cuponDescuento = data.descuento;
        msg.style.color = 'green';
        msg.textContent = data.message;
        loadResumen();
    } else {
        msg.style.color = 'red';
        msg.textContent = data.message;
    }
}

document.getElementById('checkout-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const body = {
        session_id: sessionId,
        nombre_cliente: document.getElementById('nombre').value,
        email_cliente: document.getElementById('email').value,
        telefono: document.getElementById('telefono').value,
        direccion: document.getElementById('direccion').value,
        comuna: document.getElementById('comuna').value,
        ciudad: document.getElementById('ciudad').value,
        metodo_pago: document.querySelector('input[name=metodo_pago]:checked').value,
        notas: document.getElementById('notas').value,
        cupon: document.getElementById('cupon').value,
    };
    const res = await fetch('/api/orders', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content},
        body: JSON.stringify(body)
    });
    const order = await res.json();
    if (res.ok) {
        localStorage.removeItem('cart_session');
        window.location = `/pedido/${order.id}/confirmacion`;
    } else {
        alert(order.message || 'Error al procesar el pedido');
    }
});

loadResumen();
</script>
@endpush
