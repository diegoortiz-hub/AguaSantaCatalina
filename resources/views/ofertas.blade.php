@extends('layouts.app')

@section('title', 'Ofertas — Aguas Santa Catalina')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-8">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-rose-600 to-orange-500 rounded-3xl p-6 sm:p-10 text-white relative overflow-hidden shadow-md">
        <div class="relative z-10 max-w-xl">
            <span class="text-xs font-bold uppercase tracking-wider text-rose-100">Ofertas Especiales</span>
            <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight mt-1">Precios irresistibles</h1>
            <p class="text-xs sm:text-sm text-rose-100 mt-2">Aprovecha nuestras ofertas por tiempo limitado. Descuentos reales en productos seleccionados.</p>
        </div>
        <div class="absolute right-4 bottom-4 text-white opacity-10 pointer-events-none">
            <svg class="w-48 h-48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
        </div>
    </div>

    {{-- Cupón recordatorio --}}
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-center gap-3">
        <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
        <p class="text-xs text-amber-800 font-medium">
            <strong>¿Tienes cupón?</strong> Usa <code class="bg-amber-200 px-1.5 py-0.5 rounded font-mono">PURASALUD10</code> o <code class="bg-amber-200 px-1.5 py-0.5 rounded font-mono">AGUA15</code> en el carrito para descuentos adicionales de 10% y 15%.
        </p>
    </div>

    @if($products->isEmpty())
    {{-- Sin ofertas activas --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
        <h3 class="text-base font-bold text-slate-800">No hay ofertas activas en este momento</h3>
        <p class="text-xs text-slate-500 mt-1">Vuelve pronto o revisa nuestro catálogo completo.</p>
        <a href="{{ route('catalogo') }}"
           class="inline-block mt-4 px-5 py-2.5 bg-cyan-600 text-white font-bold text-xs rounded-xl hover:bg-cyan-700 transition-all">
            Ver Catálogo Completo
        </a>
    </div>
    @else
    {{-- Grid de productos en oferta --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
        @foreach($products as $product)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow group">
            {{-- Badge descuento --}}
            @php
                $descuento = round((1 - $product->precio / $product->precio_original) * 100);
            @endphp
            <div class="relative">
                <div class="absolute top-2 left-2 z-10 px-2 py-0.5 bg-rose-600 text-white text-[10px] font-black rounded-full">
                    -{{ $descuento }}%
                </div>
                <div class="aspect-square bg-slate-50 flex items-center justify-center p-4">
                    @if($product->imagen)
                    <img src="{{ Storage::url($product->imagen) }}" alt="{{ $product->nombre }}"
                         class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
                    @else
                    <div class="w-16 h-16 rounded-full bg-cyan-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    @endif
                </div>
            </div>
            <div class="p-3 sm:p-4 space-y-2">
                <h3 class="text-xs font-bold text-slate-800 line-clamp-2 leading-snug">{{ $product->nombre }}</h3>
                <div class="flex items-end gap-2">
                    <span class="text-base font-extrabold text-cyan-700">${{ number_format($product->precio, 0, ',', '.') }}</span>
                    <span class="text-xs text-slate-400 line-through">${{ number_format($product->precio_original, 0, ',', '.') }}</span>
                </div>
                <button onclick="$store.cart.add({ id: {{ $product->id }}, nombre: '{{ addslashes($product->nombre) }}', precio: {{ $product->precio }}, imagen: '{{ $product->imagen ? Storage::url($product->imagen) : '' }}' })"
                        class="w-full py-2 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-bold transition-all">
                    Agregar al carrito
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
