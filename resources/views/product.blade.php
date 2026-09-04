@extends('layouts.app')
@section('title', $product->nombre)

@section('content')

{{-- Breadcrumb --}}
<div class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-[#0A3D7A] transition">Inicio</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('productos.index') }}" class="hover:text-[#0A3D7A] transition">Productos</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('productos.index', ['categoria'=>$product->category->slug]) }}" class="hover:text-[#0A3D7A] transition">{{ $product->category->nombre }}</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-[#0A3D7A] font-medium truncate max-w-xs">{{ $product->nombre }}</span>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-10"
     x-data="{ qty: 1, activeImg: 0 }">

    <div class="grid lg:grid-cols-2 gap-12">

        {{-- ── GALLERY ──────────────────────────────────────────────────────── --}}
        <div class="space-y-3">
            {{-- Main image --}}
            <div class="card overflow-hidden" style="background:linear-gradient(135deg,#EFF6FF,#DBEAFE);height:420px;display:flex;align-items:center;justify-content:center;position:relative;">
                @if($product->badge)
                <span class="absolute top-4 left-4 badge badge-{{ $product->badge_color ?? 'blue' }} text-sm px-3 py-1">{{ $product->badge }}</span>
                @endif
                @if($product->tieneDescuento())
                <span class="absolute top-4 right-4 badge badge-red text-sm px-3 py-1">-{{ $product->porcentajeDescuento() }}% OFF</span>
                @endif
                <svg class="w-44 h-44 text-[#1a56c4]/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>

            {{-- Thumbnails --}}
            <div class="grid grid-cols-4 gap-2">
                @for($i=0;$i<4;$i++)
                <button @click="activeImg={{ $i }}"
                    class="card overflow-hidden transition-all"
                    :style="activeImg === {{ $i }} ? 'border:2px solid #1a56c4;' : 'border:2px solid transparent;'"
                    style="height:80px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#EFF6FF,#DBEAFE);">
                    <svg class="w-8 h-8 {{ $i === 0 ? 'text-[#1a56c4]' : 'text-[#1a56c4]/30' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </button>
                @endfor
            </div>
        </div>

        {{-- ── PRODUCT INFO ─────────────────────────────────────────────────── --}}
        <div>
            <p class="text-sm font-semibold text-[#1a56c4] uppercase tracking-wide mb-2">{{ $product->category->nombre }}</p>
            <h1 class="text-3xl font-black text-[#0A3D7A] mb-3 leading-tight" style="font-family:'Poppins',sans-serif;">{{ $product->nombre }}</h1>

            {{-- Stars + SKU --}}
            <div class="flex items-center gap-3 mb-4">
                <span class="stars">★★★★★</span>
                <span class="text-sm text-gray-500">({{ rand(22,148) }} reseñas)</span>
                @if($product->sku)
                <span class="text-xs text-gray-300">|</span>
                <span class="text-xs text-gray-400">SKU: {{ $product->sku }}</span>
                @endif
            </div>

            {{-- Stock badge --}}
            <div class="mb-4">
                @if($product->stock === 0)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold bg-red-50 text-red-700">
                    <span class="w-2 h-2 bg-red-500 rounded-full"></span> Agotado
                </span>
                @elseif($product->stockBajo())
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold bg-orange-50 text-orange-700">
                    <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span> ¡Solo {{ $product->stock }} disponibles!
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold bg-green-50 text-green-700">
                    <span class="w-2 h-2 bg-green-500 rounded-full"></span> En stock ({{ $product->stock }} uds.)
                </span>
                @endif
            </div>

            {{-- Price --}}
            <div class="flex items-baseline gap-3 mb-2">
                <span class="text-4xl font-black text-[#0A3D7A]" style="font-family:'Poppins',sans-serif;">${{ number_format($product->precio, 0, ',', '.') }}</span>
                @if($product->precio_original)
                <span class="text-xl text-gray-400 line-through">${{ number_format($product->precio_original, 0, ',', '.') }}</span>
                @endif
            </div>
            @if($product->tieneDescuento())
            <p class="text-sm text-[#1FA855] font-semibold mb-4 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Ahorras ${{ number_format($product->precio_original - $product->precio, 0, ',', '.') }} ({{ $product->porcentajeDescuento() }}% de descuento)
            </p>
            @endif

            {{-- Description --}}
            <p class="text-gray-600 leading-relaxed mb-6">{{ $product->descripcion }}</p>

            {{-- Quantity + Add to cart --}}
            @if($product->stock > 0)
            <div class="flex items-center gap-3 mb-4">
                <div class="flex items-center border-2 border-gray-200 rounded-lg overflow-hidden">
                    <button @click="qty = Math.max(1, qty - 1)"
                        class="px-4 py-3 text-gray-600 hover:bg-gray-50 font-bold text-lg transition">−</button>
                    <span x-text="qty" class="px-4 py-3 min-w-[3rem] text-center font-semibold text-[#0A3D7A]"></span>
                    <button @click="qty = Math.min({{ $product->stock }}, qty + 1)"
                        class="px-4 py-3 text-gray-600 hover:bg-gray-50 font-bold text-lg transition">+</button>
                </div>
                <button @click="$store.cart.add({{ $product->id }}, '{{ addslashes($product->nombre) }}', {{ $product->precio }}, qty)"
                    class="btn-primary flex-1 text-base py-3.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    AGREGAR AL CARRITO
                </button>
            </div>
            <a href="https://wa.me/56981493272?text=Hola!%20Quiero%20pedir%20{{ urlencode($product->nombre) }}" target="_blank"
               class="btn-whatsapp w-full justify-center text-base py-3.5 mb-6">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Pedir por WhatsApp
            </a>
            @else
            <div class="py-4 px-6 rounded-xl bg-gray-50 text-gray-500 text-center mb-6 font-medium">
                Producto no disponible en este momento
            </div>
            @endif

            {{-- Trust badges --}}
            @php
            $trustBadges = [
                ['M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1.75 12.5A2 2 0 008.73 22h6.54a2 2 0 001.98-1.5L19 8', 'Entrega 24h', 'En Santiago y RM', '#1a56c4'],
                ['M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'Pago seguro', 'Webpay y transferencia', '#1FA855'],
                ['M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'Bidón retornable', 'Eco-friendly', '#059669'],
                ['M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'Calidad garantizada', 'O te devolvemos tu dinero', '#7C3AED'],
            ];
            @endphp
            <div class="grid grid-cols-2 gap-3 mb-6">
                @foreach($trustBadges as [$path, $t, $s, $color])
                <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-xl">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:{{ $color }}15;">
                        <svg class="w-4 h-4" fill="none" stroke="{{ $color }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $path }}"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-700">{{ $t }}</div>
                        <div class="text-[10px] text-gray-400">{{ $s }}</div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Specs --}}
            @if($product->specs)
            <div class="card overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-sm text-[#0A3D7A]" style="font-family:'Poppins',sans-serif;">Especificaciones</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($product->specs as $k => $v)
                    <div class="flex px-4 py-2.5 text-sm">
                        <span class="w-40 font-medium text-gray-500 shrink-0">{{ $k }}</span>
                        <span class="text-gray-800">{{ $v }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ── RELATED PRODUCTS ─────────────────────────────────────────────────── --}}
    @if($related->isNotEmpty())
    <section class="mt-16">
        <h2 class="text-2xl font-bold section-title mb-6">Productos Relacionados</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($related as $r)
            <div class="card flex flex-col group" x-data>
                <div class="relative overflow-hidden" style="background:linear-gradient(135deg,#EFF6FF,#DBEAFE);height:140px;display:flex;align-items:center;justify-content:center;">
                    <div class="group-hover:scale-110 transition-transform">
                        <svg class="w-12 h-12 text-[#1a56c4]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                </div>
                <div class="p-3 flex flex-col flex-1">
                    <a href="{{ route('productos.show', $r->slug) }}" class="text-sm font-semibold text-gray-800 hover:text-[#0A3D7A] flex-1 mb-2 line-clamp-2" style="font-family:'Poppins',sans-serif;">{{ $r->nombre }}</a>
                    <span class="text-lg font-black text-[#0A3D7A]" style="font-family:'Poppins',sans-serif;">${{ number_format($r->precio, 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection
