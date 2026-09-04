@php use Illuminate\Support\Facades\Storage; use Illuminate\Support\Str; @endphp
@extends('layouts.app')
@section('title', 'Inicio')

@section('content')

{{-- ── HERO ─────────────────────────────────────────────────────────────────── --}}
@if($banners->isNotEmpty())
{{-- Banner Carousel --}}
<section
    class="relative overflow-hidden"
    style="min-height:520px;"
    x-data="{
        current: 0,
        total: {{ $banners->count() }},
        timer: null,
        init() {
            this.startTimer();
        },
        startTimer() {
            this.timer = setInterval(() => { this.next(); }, 5000);
        },
        resetTimer() {
            clearInterval(this.timer);
            this.startTimer();
        },
        next() { this.current = (this.current + 1) % this.total; },
        prev() { this.current = (this.current - 1 + this.total) % this.total; },
        go(i) { this.current = i; this.resetTimer(); }
    }"
>
    {{-- Slides --}}
    @foreach($banners as $i => $banner)
    @php
        $imgSrc = Str::startsWith($banner->imagen, 'http') ? $banner->imagen : Storage::url($banner->imagen);
    @endphp
    <div
        class="absolute inset-0 transition-opacity duration-700"
        x-show="current === {{ $i }}"
        x-transition:enter="transition-opacity duration-700"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-700"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="min-height:520px;"
    >
        {{-- Background image --}}
        <div class="absolute inset-0 bg-cover bg-center" style="background-image:url('{{ $imgSrc }}');"></div>
        <div class="absolute inset-0" style="background:linear-gradient(to right, rgba(10,61,122,0.85) 0%, rgba(10,61,122,0.4) 60%, transparent 100%);"></div>

        {{-- Content --}}
        <div class="relative z-10 max-w-7xl mx-auto px-4 h-full flex items-center" style="min-height:520px;">
            <div class="max-w-xl py-16 md:py-24">
                @if($banner->titulo)
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-tight mb-4" style="font-family:'Poppins',sans-serif;">
                    {!! nl2br(e($banner->titulo)) !!}
                </h1>
                @endif
                @if($banner->subtitulo)
                <p class="text-white/85 text-lg leading-relaxed mb-8 max-w-md">
                    {{ $banner->subtitulo }}
                </p>
                @endif
                @if($banner->link)
                <a href="{{ $banner->link }}" class="btn-primary text-base px-8 py-4 inline-flex items-center gap-2">
                    Ver más
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endif
            </div>
        </div>
    </div>
    @endforeach

    {{-- Prev / Next arrows --}}
    @if($banners->count() > 1)
    <button
        @click="prev(); resetTimer();"
        class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-black/30 hover:bg-black/50 text-white flex items-center justify-center transition backdrop-blur-sm"
        aria-label="Anterior"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <button
        @click="next(); resetTimer();"
        class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-black/30 hover:bg-black/50 text-white flex items-center justify-center transition backdrop-blur-sm"
        aria-label="Siguiente"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>

    {{-- Dot indicators --}}
    <div class="absolute bottom-5 left-1/2 -translate-x-1/2 z-20 flex gap-2">
        @foreach($banners as $i => $banner)
        <button
            @click="go({{ $i }})"
            class="transition-all rounded-full"
            :class="current === {{ $i }} ? 'w-6 h-2.5 bg-white' : 'w-2.5 h-2.5 bg-white/50 hover:bg-white/80'"
            aria-label="Ir a banner {{ $i + 1 }}"
        ></button>
        @endforeach
    </div>
    @endif
</section>

@else
{{-- Static fallback hero --}}
<section class="relative overflow-hidden" style="min-height:520px; background:linear-gradient(135deg,#0A3D7A 0%,#1E6FBF 60%,#0e5099 100%);">
    {{-- Decorative circles --}}
    <div class="absolute -top-20 -right-20 w-96 h-96 rounded-full opacity-10" style="background:radial-gradient(circle,#fff 0%,transparent 70%);"></div>
    <div class="absolute -bottom-16 -left-16 w-72 h-72 rounded-full opacity-10" style="background:radial-gradient(circle,#fff 0%,transparent 70%);"></div>
    <div class="absolute top-1/2 right-1/4 w-2 h-2 bg-white/30 rounded-full"></div>
    <div class="absolute top-1/4 right-1/3 w-1 h-1 bg-white/40 rounded-full"></div>

    <div class="max-w-7xl mx-auto px-4 py-16 md:py-24 relative z-10">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <span class="inline-flex items-center gap-2 bg-white/15 text-white text-xs font-semibold px-3 py-1.5 rounded-full mb-6">
                    <span class="w-1.5 h-1.5 bg-[#25D366] rounded-full animate-pulse"></span>
                    Agua certificada · Entrega en 24h
                </span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-tight mb-6" style="font-family:'Poppins',sans-serif;">
                    AGUA PURA<br>
                    <span style="color:#7dd3fc;">PARA TU HOGAR</span><br>
                    Y EMPRESA
                </h1>
                <p class="text-white/80 text-lg leading-relaxed mb-8 max-w-md">
                    Agua purificada por osmosis inversa, con entrega a domicilio en Santiago. Calidad certificada para tu familia y negocio.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('productos.index') }}" class="btn-primary text-base px-8 py-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Comprar Ahora
                    </a>
                    <a href="{{ route('productos.index') }}" class="inline-flex items-center gap-2 px-8 py-4 text-base font-semibold text-white border-2 border-white/50 rounded-lg hover:border-white hover:bg-white/10 transition">
                        Ver Productos
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                {{-- Trust stats --}}
                <div class="flex gap-8 mt-10">
                    @foreach([['15+','Años de experiencia'],['5k+','Clientes felices'],['100%','Agua certificada']] as [$n,$l])
                    <div>
                        <div class="text-2xl font-black text-white" style="font-family:'Poppins',sans-serif;">{{ $n }}</div>
                        <div class="text-xs text-white/60">{{ $l }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Hero visual --}}
            <div class="hidden md:flex items-center justify-center">
                <div class="relative w-72 h-72">
                    <div class="w-72 h-72 rounded-full opacity-15 absolute inset-0" style="background:radial-gradient(circle,#7dd3fc,transparent);"></div>
                    {{-- SVG bidón de agua estilizado --}}
                    <div class="relative z-10 flex flex-col items-center justify-center h-full">
                        <svg width="160" height="200" viewBox="0 0 160 200" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-2xl">
                            {{-- Tapa --}}
                            <rect x="55" y="8" width="50" height="16" rx="8" fill="rgba(255,255,255,0.35)"/>
                            {{-- Cuerpo --}}
                            <rect x="28" y="24" width="104" height="148" rx="20" fill="rgba(255,255,255,0.12)" stroke="rgba(255,255,255,0.4)" stroke-width="2"/>
                            {{-- Nivel de agua --}}
                            <rect x="30" y="82" width="100" height="88" rx="0 0 18 18" fill="rgba(125,211,252,0.35)"/>
                            <path d="M30 82 Q80 70 130 82" stroke="rgba(255,255,255,0.5)" stroke-width="1.5" fill="none"/>
                            {{-- Etiqueta --}}
                            <rect x="44" y="44" width="72" height="50" rx="8" fill="rgba(255,255,255,0.18)"/>
                            <rect x="52" y="52" width="56" height="5" rx="2.5" fill="rgba(255,255,255,0.6)"/>
                            <rect x="56" y="62" width="48" height="3" rx="1.5" fill="rgba(255,255,255,0.35)"/>
                            <rect x="56" y="70" width="36" height="3" rx="1.5" fill="rgba(255,255,255,0.35)"/>
                            {{-- Asa --}}
                            <path d="M28 60 Q8 60 8 90 Q8 120 28 120" stroke="rgba(255,255,255,0.4)" stroke-width="6" fill="none" stroke-linecap="round"/>
                            {{-- Grifo --}}
                            <rect x="116" y="148" width="20" height="10" rx="3" fill="rgba(255,255,255,0.3)"/>
                            <rect x="130" y="152" width="12" height="5" rx="2.5" fill="rgba(255,255,255,0.4)"/>
                        </svg>
                        <div class="mt-1 bg-white/20 backdrop-blur-sm rounded-2xl px-6 py-3 text-white text-center">
                            <div class="text-2xl font-black" style="font-family:'Poppins',sans-serif;">$3.990</div>
                            <div class="text-xs text-white/70">Bidón 20L · Precio por unidad</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ── BENEFITS BAR ─────────────────────────────────────────────────────────── --}}
<section class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-4">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-0 divide-x divide-gray-100">
            @foreach([
                ['M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z','Agua certificada','NSF · ISO 9001','#0A3D7A'],
                ['M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4','Entrega rápida','24h en Santiago','#1a56c4'],
                ['M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z','Pago seguro','Webpay · Transferencia','#1a56c4'],
                ['M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z','Atención personalizada','L–S 8–20h','#0A3D7A'],
                ['M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15','Compromiso Ambiental','Bidones retornables','#1FA855'],
            ] as [$ico,$title,$sub,$color])
            <div class="flex items-center gap-2.5 px-4 py-3">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:{{ $color }}15;">
                    <svg class="w-4 h-4" style="color:{{ $color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $ico }}"/></svg>
                </div>
                <div>
                    <div class="text-xs font-bold text-[#0A3D7A]" style="font-family:'Poppins',sans-serif;">{{ $title }}</div>
                    <div class="text-[10px] text-gray-400">{{ $sub }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── CATEGORIES ───────────────────────────────────────────────────────────── --}}
<section class="max-w-7xl mx-auto px-4 py-14">
    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="text-sm font-semibold text-[#1a56c4] mb-1 uppercase tracking-wide">Explora por categoría</p>
            <h2 class="text-3xl font-bold section-title">Nuestras Categorías</h2>
        </div>
        <a href="{{ route('productos.index') }}" class="hidden sm:flex items-center gap-1 text-sm font-semibold text-[#1a56c4] hover:text-[#0A3D7A] transition">
            Ver todo <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        @php
        $catIcons = [
            'agua-purificada'   => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
            'dispensadores'     => 'M3 10h11M9 21V3m0 0l3 3M9 3L6 6m12 3v12a2 2 0 01-2 2H7a2 2 0 01-2-2V9',
            'bombas'            => 'M13 10V3L4 14h7v7l9-11h-7z',
            'accesorios'        => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
            'repuestos-y-filtros'=> 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
            'packs-y-promos'    => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        ];
        $catColors = [
            'agua-purificada'   => '#0A3D7A',
            'dispensadores'     => '#1a56c4',
            'bombas'            => '#d97706',
            'accesorios'        => '#6d28d9',
            'repuestos-y-filtros'=> '#059669',
            'packs-y-promos'    => '#db2777',
        ];
        @endphp
        @foreach($categories as $cat)
        @php
            $ico   = $catIcons[$cat->slug] ?? 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4';
            $color = $catColors[$cat->slug] ?? '#1a56c4';
        @endphp
        <a href="{{ route('productos.index', ['categoria'=>$cat->slug]) }}"
           class="card group flex flex-col items-center text-center p-5 hover:border-[#1a56c4]/30 hover:shadow-md transition-all cursor-pointer">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-3 transition-transform group-hover:scale-110"
                 style="background:{{ $color }}15;">
                <svg class="w-6 h-6" style="color:{{ $color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $ico }}"/>
                </svg>
            </div>
            <div class="text-sm font-semibold text-[#0A3D7A] leading-tight mb-1" style="font-family:'Poppins',sans-serif;">{{ $cat->nombre }}</div>
            <div class="text-xs text-gray-400 mt-auto">{{ $cat->products_count }} productos</div>
        </a>
        @endforeach
    </div>
</section>

{{-- ── FEATURED PRODUCTS ────────────────────────────────────────────────────── --}}
<section class="bg-white py-14">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-end justify-between mb-8">
            <div>
                <p class="text-sm font-semibold text-[#1a56c4] mb-1 uppercase tracking-wide">Los más pedidos</p>
                <h2 class="text-3xl font-bold section-title">Productos Destacados</h2>
            </div>
            <a href="{{ route('productos.index', ['destacado'=>1]) }}" class="hidden sm:flex items-center gap-1 text-sm font-semibold text-[#1a56c4] hover:text-[#0A3D7A] transition">
                Ver todos <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($destacados as $p)
            <div class="card flex flex-col group" x-data>
                {{-- Image --}}
                <div class="relative overflow-hidden" style="background:linear-gradient(135deg,#EFF6FF,#DBEAFE);height:160px;display:flex;align-items:center;justify-content:center;">
                    @if($p->badge)
                    <span class="absolute top-2 left-2 badge badge-{{ $p->badge_color ?? 'blue' }} z-10">{{ $p->badge }}</span>
                    @endif
                    @if($p->precio_original && $p->precio_original > $p->precio)
                    <span class="absolute top-2 right-2 badge badge-red z-10">-{{ $p->porcentajeDescuento() }}%</span>
                    @endif
                    <div class="group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-16 h-16 text-[#1a56c4]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    {{-- Quick add --}}
                    <button @click="$store.cart.add({{ $p->id }}, '{{ addslashes($p->nombre) }}', {{ $p->precio }})"
                        class="absolute bottom-2 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity btn-primary text-xs px-4 py-1.5 whitespace-nowrap shadow">
                        + Agregar
                    </button>
                </div>
                {{-- Info --}}
                <div class="p-3 flex flex-col flex-1">
                    <p class="text-[10px] text-[#1a56c4] font-medium mb-0.5">{{ $p->category->nombre }}</p>
                    <a href="{{ route('productos.show', $p->slug) }}" class="text-xs font-semibold text-gray-800 hover:text-[#0A3D7A] leading-tight mb-2 line-clamp-2 flex-1" style="font-family:'Poppins',sans-serif;">
                        {{ $p->nombre }}
                    </a>
                    <div class="stars text-xs mb-1">★★★★★ <span class="text-gray-400 font-normal" style="font-family:'Inter',sans-serif;">({{ rand(12,98) }})</span></div>
                    <div class="flex items-baseline gap-1.5 mb-3">
                        <span class="text-lg font-black text-[#0A3D7A]" style="font-family:'Poppins',sans-serif;">${{ number_format($p->precio, 0, ',', '.') }}</span>
                        @if($p->precio_original)
                        <span class="text-xs text-gray-400 line-through">${{ number_format($p->precio_original, 0, ',', '.') }}</span>
                        @endif
                    </div>
                    <button @click="$store.cart.add({{ $p->id }}, '{{ addslashes($p->nombre) }}', {{ $p->precio }})"
                        class="w-full btn-primary text-xs py-2 justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Al carrito
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── PROMO BANNERS ────────────────────────────────────────────────────────── --}}
<section class="max-w-7xl mx-auto px-4 py-12">
    <div class="grid md:grid-cols-2 gap-6">
        {{-- Banner 1: Ofertas --}}
        <div class="card overflow-hidden" style="background:linear-gradient(135deg,#0A3D7A 0%,#1E6FBF 100%);min-height:180px;">
            <div class="p-8 flex items-center justify-between h-full">
                <div>
                    <span class="badge badge-orange mb-3">OFERTA ESPECIAL</span>
                    <h3 class="text-2xl font-black text-white mb-2" style="font-family:'Poppins',sans-serif;">Pack 4 Bidones 20L</h3>
                    <p class="text-white/80 text-sm mb-4">Ahorra $1.970 comprando el pack familiar</p>
                    <a href="{{ route('productos.show', 'pack-familiar-4-bidones-20l') }}" class="inline-flex items-center gap-2 bg-white text-[#0A3D7A] font-bold text-sm px-5 py-2.5 rounded-lg hover:bg-gray-100 transition">
                        Ver oferta <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <svg class="w-20 h-20 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>

        {{-- Banner 2: Empresas --}}
        <div class="card overflow-hidden" style="background:linear-gradient(135deg,#F0FDF4 0%,#DCFCE7 100%);min-height:180px; border:2px solid #BBF7D0;">
            <div class="p-8 flex items-center justify-between h-full">
                <div>
                    <span class="badge badge-green mb-3">PARA EMPRESAS</span>
                    <h3 class="text-2xl font-black text-[#0A3D7A] mb-2" style="font-family:'Poppins',sans-serif;">Soluciones Corporativas</h3>
                    <p class="text-gray-600 text-sm mb-4">Precios especiales por volumen y contratos mensuales</p>
                    <a href="https://wa.me/56981493272?text=Hola!%20Me%20interesa%20una%20cotización%20para%20empresa" target="_blank" class="btn-whatsapp text-sm px-5 py-2.5">
                        Cotizar ahora
                    </a>
                </div>
                <svg class="w-20 h-20 text-[#0A3D7A]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
        </div>
    </div>
</section>

{{-- ── CLIENT LOGOS ─────────────────────────────────────────────────────────── --}}
<section id="nosotros" class="bg-white py-12 border-y border-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        <p class="text-center text-sm text-gray-400 font-medium uppercase tracking-widest mb-8">Empresas que confían en nosotros</p>
        <div class="flex flex-wrap items-center justify-center gap-8 md:gap-12">
            @foreach(['COPEC','CENCOSUD','WALMART','NESTLÉ','UNIMARC','SODEXO'] as $brand)
            <div class="h-10 flex items-center justify-center">
                <span class="text-gray-300 font-black text-lg tracking-widest hover:text-gray-400 transition" style="font-family:'Poppins',sans-serif;">{{ $brand }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── WHY US ───────────────────────────────────────────────────────────────── --}}
<section class="max-w-7xl mx-auto px-4 py-14">
    <div class="text-center mb-10">
        <p class="text-sm font-semibold text-[#1a56c4] mb-1 uppercase tracking-wide">¿Por qué elegirnos?</p>
        <h2 class="text-3xl font-bold section-title">Calidad que se siente</h2>
    </div>
    @php
    $whyUs = [
        ['M12 2C12 2 5 10 5 14a7 7 0 0014 0c0-4-7-12-7-12z', 'Agua Pura', 'Purificada por osmosis inversa y UV. Sin bacterias, sin metales pesados.', '#1a56c4'],
        ['M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1.75 12.5A2 2 0 008.73 22h6.54a2 2 0 001.98-1.5L19 8', 'Entrega Rápida', 'Despacho en 24 horas hábiles dentro de Santiago y RM.', '#0A3D7A'],
        ['M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'Eco-friendly', 'Bidones retornables para reducir el impacto ambiental.', '#059669'],
        ['M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'Garantía', 'Si no quedas satisfecho, devolvemos tu dinero. Sin preguntas.', '#7C3AED'],
    ];
    @endphp
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($whyUs as [$path, $title, $desc, $color])
        <div class="card p-6 text-center">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:{{ $color }}15;">
                <svg class="w-7 h-7" fill="none" stroke="{{ $color }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $path }}"/></svg>
            </div>
            <h3 class="font-bold text-[#0A3D7A] mb-2" style="font-family:'Poppins',sans-serif;">{{ $title }}</h3>
            <p class="text-sm text-gray-500 leading-relaxed">{{ $desc }}</p>
        </div>
        @endforeach
    </div>
</section>

@endsection
