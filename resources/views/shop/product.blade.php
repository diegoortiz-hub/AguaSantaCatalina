@extends('layouts.app')

@section('title', $product->nombre)

@section('content')
<div style="display:grid; grid-template-columns:1fr 1fr; gap:2rem; align-items:start;">
    <!-- Imagen -->
    <div style="background:#e9f5ff; border-radius:12px; height:350px; display:flex; align-items:center; justify-content:center; font-size:6rem;">💧</div>

    <!-- Info -->
    <div>
        <p style="color:#0077b6; margin-bottom:.5rem;">{{ $product->category->nombre }}</p>
        <h1 style="font-size:1.8rem; margin-bottom:.5rem;">{{ $product->nombre }}</h1>

        @if($product->badge)
            <span class="badge badge-{{ $product->badge_color }}" style="margin-bottom:1rem;">{{ $product->badge }}</span>
        @endif

        <p style="font-size:2rem; font-weight:700; color:#0077b6; margin:1rem 0;">
            ${{ number_format($product->precio, 0, ',', '.') }}
        </p>
        @if($product->precio_original)
            <p style="color:#999; text-decoration:line-through;">Antes: ${{ number_format($product->precio_original, 0, ',', '.') }}</p>
            <p style="color:#2d6a4f; font-weight:700;">Ahorro: {{ $product->porcentajeDescuento() }}% off</p>
        @endif

        <p style="margin:1rem 0; color:#555;">{{ $product->descripcion }}</p>

        <p style="color:{{ $product->stock <= $product->stock_minimo ? '#d62828' : '#2d6a4f' }}; margin-bottom:1rem; font-weight:600;">
            @if($product->stock === 0) Agotado
            @elseif($product->stockBajo()) ⚠ Pocas unidades ({{ $product->stock }} disponibles)
            @else ✓ En stock ({{ $product->stock }} disponibles)
            @endif
        </p>

        @if($product->stock > 0)
        <div style="display:flex; gap:1rem; flex-wrap:wrap;">
            <button class="btn btn-primary" onclick="addToCart({{ $product->id }})">🛒 Agregar al carrito</button>
            <a href="https://wa.me/56999999999?text=Hola!%20Quiero%20pedir%20{{ urlencode($product->nombre) }}"
               class="btn btn-whatsapp" target="_blank">💬 Pedir por WhatsApp</a>
        </div>
        @else
            <p class="btn" style="background:#eee; color:#888;">Sin stock disponible</p>
        @endif

        @if($product->specs)
        <div style="margin-top:1.5rem; border-top:1px solid #eee; padding-top:1rem;">
            <h3 style="margin-bottom:.8rem;">Especificaciones</h3>
            <table style="width:100%; border-collapse:collapse;">
                @foreach($product->specs as $key => $value)
                <tr style="border-bottom:1px solid #f0f0f0;">
                    <td style="padding:.4rem; color:#555; font-weight:600; width:45%;">{{ $key }}</td>
                    <td style="padding:.4rem;">{{ $value }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif
    </div>
</div>

@if($related->isNotEmpty())
<section style="margin-top:3rem;">
    <h2 style="margin-bottom:1.5rem;">Productos relacionados</h2>
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:1.2rem;">
        @foreach($related as $r)
        <a href="{{ route('productos.show', $r->slug) }}" style="display:block; border:1px solid #eee; border-radius:8px; padding:1rem; text-decoration:none; color:inherit;">
            <div style="height:80px; background:#e9f5ff; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:2rem; margin-bottom:.8rem;">💧</div>
            <p style="font-size:.9rem; font-weight:600;">{{ $r->nombre }}</p>
            <p style="color:#0077b6; font-weight:700;">${{ number_format($r->precio, 0, ',', '.') }}</p>
        </a>
        @endforeach
    </div>
</section>
@endif
@endsection

@push('scripts')
<script>
function addToCart(productId) {
    const sessionId = localStorage.getItem('cart_session') || crypto.randomUUID();
    localStorage.setItem('cart_session', sessionId);

    fetch('/api/cart', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
        body: JSON.stringify({ session_id: sessionId, product_id: productId, cantidad: 1 })
    })
    .then(r => r.json())
    .then(() => alert('✅ Producto agregado al carrito'))
    .catch(() => alert('Error al agregar al carrito'));
}
</script>
@endpush
