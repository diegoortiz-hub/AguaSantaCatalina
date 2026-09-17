<!DOCTYPE html>
<html lang="es" x-data>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light">
    <meta name="darkreader-lock">
    <title>@yield('title', 'Aguas Santa Catalina') — Agua Pura para tu Hogar</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script>window.TIENDA = @json($ajustes->publicos());</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-[#F8FAFC] font-sans">

{{-- ── Topbar ───────────────────────────────────────────────────────────────── --}}
<div class="bg-[#0A3D7A] text-white text-xs py-2">
    <div class="max-w-7xl mx-auto px-4 flex items-center justify-between gap-4">
        <span class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2L19 8m-9 4h4"/></svg>
            Despacho gratis sobre ${{ number_format($ajustes->get('despacho_gratis'), 0, ',', '.') }} en Santiago
        </span>
        <span class="hidden sm:flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            {{ $ajustes->get('telefono') }}
        </span>
        <span class="hidden md:flex items-center gap-3 text-white/80">
            <span>Lun–Sáb 8–20h</span>
            <span>·</span>
            <span>info@aguassantacatalina.cl</span>
        </span>
    </div>
</div>

{{-- ── Header ───────────────────────────────────────────────────────────────── --}}
<header class="bg-white shadow-sm sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-4">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center shrink-0" aria-label="Aguas Santa Catalina — Inicio">
            <img src="{{ asset('images/logo.png') }}" alt="Aguas Santa Catalina — pura por naturaleza"
                 class="h-10 sm:h-12 w-auto">
        </a>

        {{-- Search --}}
        <form action="{{ route('productos.index') }}" method="GET" class="flex-1 max-w-xl hidden md:flex">
            <div class="relative w-full">
                <input type="text" name="q" value="{{ request('q') }}"
                    placeholder="Buscar agua, dispensadores, filtros..."
                    class="w-full pl-4 pr-12 py-2.5 text-sm border border-gray-200 rounded-lg outline-none focus:border-[#1a56c4] focus:ring-2 focus:ring-[#1a56c4]/10 transition-all">
                <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 text-gray-400 hover:text-[#1a56c4]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </div>
        </form>

        {{-- Right actions --}}
        <div class="flex items-center gap-1 ml-auto">

            {{-- Acceso al panel: sin esto un administrador que inicia sesión
                 queda en la vista de cliente y tiene que escribir /admin a mano. --}}
            @auth
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="hidden sm:flex flex-col items-center p-2 rounded-lg hover:bg-sky-50 transition text-[#0A3D7A]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m4 10V11m4 6V9M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span class="text-[10px] font-semibold mt-0.5">Panel</span>
            </a>
            @endif
            @endauth

            {{-- Mi cuenta --}}
            @auth
            <a href="{{ route('account.orders') }}" class="hidden sm:flex flex-col items-center p-2 rounded-lg hover:bg-gray-50 transition text-gray-600 hover:text-[#0A3D7A]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-[10px] font-medium mt-0.5">Mi Cuenta</span>
            </a>
            @else
            <a href="{{ route('login') }}" class="hidden sm:flex flex-col items-center p-2 rounded-lg hover:bg-gray-50 transition text-gray-600 hover:text-[#0A3D7A]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-[10px] font-medium mt-0.5">Ingresar</span>
            </a>
            @endauth

            {{-- Cart (opens drawer) --}}
            <button
                @click="$store.cart.open = true"
                class="relative flex flex-col items-center p-2 rounded-lg hover:bg-gray-50 transition text-gray-600 hover:text-[#0A3D7A] cursor-pointer"
                aria-label="Abrir carrito"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span class="text-[10px] font-medium mt-0.5">Carrito</span>
                <span x-show="$store.cart.count > 0"
                      x-text="$store.cart.count"
                      class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] text-[10px] font-bold text-white rounded-full flex items-center justify-center cart-badge"
                      style="background:#DC2626;"></span>
            </button>

            {{-- WhatsApp CTA --}}
        </div>
    </div>
</header>

{{-- ── Navbar ───────────────────────────────────────────────────────────────── --}}
<nav style="background:#0A3D7A;">
    <div class="max-w-7xl mx-auto px-4">
        <ul class="flex items-center gap-1 overflow-x-auto scrollbar-none">
            @foreach(\App\Models\MenuItem::de('header') as $item)
                @php $href = $item->url(); @endphp
                @if($href)
                <li>
                    <a href="{{ $href }}" @if($item->nueva_pestana) target="_blank" rel="noopener" @endif
                       class="nav-link px-4 py-3 block text-sm font-semibold whitespace-nowrap {{ request()->fullUrlIs($href.'*') || url()->current() === $href ? 'text-white border-b-2 border-white' : '' }}">
                        {{ Str::upper($item->etiqueta) }}
                    </a>
                </li>
                @endif
            @endforeach
        </ul>
    </div>
</nav>

{{-- Buscador en móvil: el del header está oculto bajo 768px --}}
<div class="md:hidden bg-white border-b border-gray-100 px-4 py-2.5">
    <form action="{{ route('productos.index') }}" method="GET">
        <div class="relative">
            <input type="search" name="q" value="{{ request('q') }}"
                placeholder="Buscar agua, dispensadores, filtros..."
                class="w-full pl-4 pr-11 py-2.5 text-sm border border-gray-200 rounded-lg outline-none focus:border-[#1a56c4] focus:ring-2 focus:ring-[#1a56c4]/10 transition-all">
            <button type="submit" aria-label="Buscar" class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>
        </div>
    </form>
</div>

{{-- ── Page content ─────────────────────────────────────────────────────────── --}}
@if(session('success'))
<div class="max-w-7xl mx-auto px-4 mt-4">
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
        <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
</div>
@endif

@if(session('error'))
<div class="max-w-7xl mx-auto px-4 mt-4">
    <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
        <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
</div>
@endif

@yield('content')

{{-- ── Footer ───────────────────────────────────────────────────────────────── --}}
<footer id="contacto" style="background:#0A3D7A;" class="text-white mt-16">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

            {{-- Col 1: Logo + desc --}}
            <div>
                <img src="{{ asset('images/logo-blanco.png') }}" alt="Aguas Santa Catalina — pura por naturaleza"
                     class="h-14 w-auto mb-4">
                <p class="text-sm text-white/70 leading-relaxed mb-4">{{ $ajustes->get('footer_descripcion') }}</p>
                <div class="flex gap-3">
                    @if($ajustes->get('red_facebook'))
                    <a href="{{ $ajustes->get('red_facebook') }}" target="_blank" rel="noopener" aria-label="Facebook"
                       class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center hover:bg-white/20 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12a10 10 0 10-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.1 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.45 2.89h-2.33v6.99A10 10 0 0022 12z"/></svg>
                    </a>
                    @endif
                    @if($ajustes->get('red_instagram'))
                    <a href="{{ $ajustes->get('red_instagram') }}" target="_blank" rel="noopener" aria-label="Instagram"
                       class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center hover:bg-white/20 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41a3.7 3.7 0 01-1.38-.9c-.42-.42-.68-.82-.9-1.38-.16-.42-.36-1.06-.41-2.23C2.17 15.58 2.16 15.2 2.16 12s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41C8.42 2.17 8.8 2.16 12 2.16zm0 5.3a4.54 4.54 0 100 9.08 4.54 4.54 0 000-9.08zm0 7.49a2.95 2.95 0 110-5.9 2.95 2.95 0 010 5.9zm5.78-7.67a1.06 1.06 0 11-2.12 0 1.06 1.06 0 012.12 0z"/></svg>
                    </a>
                    @endif
                    <a href="https://wa.me/{{ $ajustes->whatsappNumero() }}" target="_blank" rel="noopener" aria-label="WhatsApp"
                       class="w-8 h-8 bg-[#25D366]/80 rounded-lg flex items-center justify-center hover:bg-[#25D366] transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                </div>
            </div>

            {{-- Col 2 y 3: menús administrables --}}
            @foreach(['footer_1', 'footer_2'] as $columna)
            <div>
                <h3 class="font-semibold text-sm mb-4 uppercase tracking-wide text-white/50">
                    {{ $ajustes->get($columna.'_titulo') }}
                </h3>
                <ul class="space-y-2 text-sm text-white/75">
                    @foreach(\App\Models\MenuItem::de($columna) as $item)
                        @php $href = $item->url(); @endphp
                        @if($href)
                        <li>
                            <a href="{{ $href }}" @if($item->nueva_pestana) target="_blank" rel="noopener" @endif
                               class="hover:text-white transition">{{ $item->etiqueta }}</a>
                        </li>
                        @endif
                    @endforeach
                </ul>
            </div>
            @endforeach

            {{-- Col 4: Contacto --}}
            <div>
                <h3 class="font-semibold text-sm mb-4 uppercase tracking-wide text-white/50">Contacto</h3>
                <ul class="space-y-3 text-sm text-white/75">
                    <li class="flex gap-2 items-start"><svg class="w-3.5 h-3.5 mt-0.5 shrink-0 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span>{{ collect([$ajustes->get('direccion'), $ajustes->get('comuna'), $ajustes->get('ciudad')])->filter()->unique()->implode(', ') }}</span></li>
                    <li class="flex gap-2 items-center"><svg class="w-3.5 h-3.5 shrink-0 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg><a href="tel:+56981493272" class="hover:text-white transition">{{ $ajustes->get('telefono') }}</a></li>
                    <li class="flex gap-2 items-center"><svg class="w-3.5 h-3.5 shrink-0 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg><a href="mailto:{{ $ajustes->get('email') }}" class="hover:text-white transition">{{ $ajustes->get('email') }}</a></li>
                    <li class="flex gap-2 items-center"><svg class="w-3.5 h-3.5 shrink-0 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>{{ $ajustes->get('horario') }}</span></li>
                </ul>

                {{-- Payment logos --}}
                <div class="mt-5">
                    <p class="text-xs text-white/50 mb-2 uppercase tracking-wide">Medios de pago</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Webpay','Transfer.','MercadoPago','WhatsApp'] as $pm)
                        <span class="px-2 py-1 text-[10px] font-semibold bg-white/10 rounded text-white/80">{{ $pm }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-white/10 mt-8 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-white/40">
            <span>© {{ date('Y') }} {{ $ajustes->get('empresa') }}. Todos los derechos reservados.</span>

            @php $legales = \App\Models\MenuItem::de('footer_3'); @endphp
            @if($legales->isNotEmpty())
            <div class="flex flex-wrap items-center justify-center gap-x-3 gap-y-1">
                @foreach($legales as $item)
                    @php $href = $item->url(); @endphp
                    @if($href)
                    <a href="{{ $href }}" @if($item->nueva_pestana) target="_blank" rel="noopener" @endif
                       class="hover:text-white/80 transition">{{ $item->etiqueta }}</a>
                    @endif
                @endforeach
            </div>
            @endif

            <span>Hecho en Chile</span>
        </div>
    </div>
</footer>

{{-- ── WhatsApp float ───────────────────────────────────────────────────────── --}}
<a href="https://wa.me/{{ $ajustes->whatsappNumero() }}?text=Hola!%20Quiero%20hacer%20un%20pedido" target="_blank" id="wa-float" title="Chatea con nosotros">
    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
</a>

{{-- ── Cart Drawer ───────────────────────────────────────────────────────────── --}}
@include('partials.cart-drawer')

@stack('scripts')
</body>
</html>
