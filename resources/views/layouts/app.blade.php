<!DOCTYPE html>
<html lang="es" x-data>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light">
    <meta name="darkreader-lock">
    <title>@yield('title', 'Aguas Santa Catalina') — Agua Pura para tu Hogar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-[#F8FAFC] font-sans">

{{-- ── Topbar ───────────────────────────────────────────────────────────────── --}}
<div class="bg-[#0A3D7A] text-white text-xs py-2">
    <div class="max-w-7xl mx-auto px-4 flex items-center justify-between gap-4">
        <span class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2L19 8m-9 4h4"/></svg>
            Despacho gratis sobre $15.000 en Santiago
        </span>
        <span class="hidden sm:flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            +56 9 9149 3272
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
        <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#0A3D7A;">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2C12 2 5 10 5 14a7 7 0 0014 0c0-4-7-12-7-12z"/></svg>
            </div>
            <div class="hidden sm:block leading-tight">
                <div class="font-bold text-[#0A3D7A] text-base" style="font-family:'Poppins',sans-serif;">Aguas Santa Catalina</div>
                <div class="text-[10px] text-gray-400 font-medium tracking-wide uppercase">pura por naturaleza</div>
            </div>
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
            <a href="https://wa.me/56981493272?text=Hola!%20Quiero%20hacer%20un%20pedido" target="_blank"
               class="hidden lg:flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-lg ml-2 transition"
               style="background:#25D366; border-radius:8px;">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Pedir ahora
            </a>
        </div>
    </div>
</header>

{{-- ── Navbar ───────────────────────────────────────────────────────────────── --}}
<nav style="background:#0A3D7A;">
    <div class="max-w-7xl mx-auto px-4">
        <ul class="flex items-center gap-1 overflow-x-auto scrollbar-none">
            <li><a href="{{ route('home') }}" class="nav-link px-4 py-3 block text-sm font-semibold {{ request()->routeIs('home') ? 'text-white border-b-2 border-white' : '' }}">INICIO</a></li>
            <li><a href="{{ route('productos.index') }}" class="nav-link px-4 py-3 block text-sm font-semibold {{ request()->routeIs('productos.*') ? 'text-white border-b-2 border-white' : '' }}">PRODUCTOS</a></li>
            <li><a href="{{ route('empresas') }}" class="nav-link px-4 py-3 block text-sm font-semibold {{ request()->routeIs('empresas') ? 'text-white border-b-2 border-white' : '' }}">EMPRESAS</a></li>
            <li><a href="{{ route('ofertas') }}" class="nav-link px-4 py-3 block text-sm font-semibold {{ request()->routeIs('ofertas') ? 'text-white border-b-2 border-white' : '' }}">OFERTAS</a></li>
            <li><a href="{{ route('nosotros') }}" class="nav-link px-4 py-3 block text-sm font-semibold {{ request()->routeIs('nosotros') ? 'text-white border-b-2 border-white' : '' }}">NOSOTROS</a></li>
            <li><a href="{{ route('contacto') }}" class="nav-link px-4 py-3 block text-sm font-semibold {{ request()->routeIs('contacto') ? 'text-white border-b-2 border-white' : '' }}">CONTACTO</a></li>
            <li class="ml-auto">
                <a href="https://wa.me/56991493272" target="_blank"
                   class="flex items-center gap-2 my-1.5 px-4 py-2 text-xs font-bold text-white rounded-lg"
                   style="background:#25D366;">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WHATSAPP
                </a>
            </li>
        </ul>
    </div>
</nav>

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
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2C12 2 5 10 5 14a7 7 0 0014 0c0-4-7-12-7-12z"/></svg>
                    </div>
                    <div>
                        <div class="font-bold text-base" style="font-family:'Poppins',sans-serif;">Aguas Santa Catalina</div>
                        <div class="text-[10px] text-white/60 uppercase tracking-wide">pura por naturaleza</div>
                    </div>
                </div>
                <p class="text-sm text-white/70 leading-relaxed mb-4">Distribuimos agua purificada de alta calidad a hogares y empresas en Santiago y Región Metropolitana desde 2008.</p>
                <div class="flex gap-3">
                    <a href="#" class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center hover:bg-white/20 transition text-xs">f</a>
                    <a href="#" class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center hover:bg-white/20 transition text-xs">ig</a>
                    <a href="https://wa.me/56981493272" target="_blank" class="w-8 h-8 bg-[#25D366]/80 rounded-lg flex items-center justify-center hover:bg-[#25D366] transition text-xs">wa</a>
                </div>
            </div>

            {{-- Col 2: Productos --}}
            <div>
                <h3 class="font-semibold text-sm mb-4 uppercase tracking-wide text-white/50">Productos</h3>
                <ul class="space-y-2 text-sm text-white/75">
                    @foreach([['agua-purificada','Agua Purificada'],['dispensadores','Dispensadores'],['bombas','Bombas'],['accesorios','Accesorios'],['repuestos-y-filtros','Filtros'],['packs-y-promos','Packs y Promos']] as [$s,$n])
                    <li><a href="{{ route('productos.index', ['categoria'=>$s]) }}" class="hover:text-white transition">{{ $n }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Col 3: Empresa --}}
            <div>
                <h3 class="font-semibold text-sm mb-4 uppercase tracking-wide text-white/50">Empresa</h3>
                <ul class="space-y-2 text-sm text-white/75">
                    <li><a href="#nosotros" class="hover:text-white transition">Nosotros</a></li>
                    <li><a href="#" class="hover:text-white transition">Términos y Condiciones</a></li>
                    <li><a href="#" class="hover:text-white transition">Política de Privacidad</a></li>
                    <li><a href="#" class="hover:text-white transition">Despacho y Devoluciones</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-white transition">Mi Cuenta</a></li>
                </ul>
            </div>

            {{-- Col 4: Contacto --}}
            <div>
                <h3 class="font-semibold text-sm mb-4 uppercase tracking-wide text-white/50">Contacto</h3>
                <ul class="space-y-3 text-sm text-white/75">
                    <li class="flex gap-2 items-start"><svg class="w-3.5 h-3.5 mt-0.5 shrink-0 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span>Santiago, Región Metropolitana, Chile</span></li>
                    <li class="flex gap-2 items-center"><svg class="w-3.5 h-3.5 shrink-0 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg><a href="tel:+56981493272" class="hover:text-white transition">+56 9 8149 3272</a></li>
                    <li class="flex gap-2 items-center"><svg class="w-3.5 h-3.5 shrink-0 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg><a href="mailto:info@aguassantacatalina.cl" class="hover:text-white transition">info@aguassantacatalina.cl</a></li>
                    <li class="flex gap-2 items-center"><svg class="w-3.5 h-3.5 shrink-0 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>Lun–Sáb: 08:00–20:00</span></li>
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

        <div class="border-t border-white/10 mt-8 pt-6 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-white/40">
            <span>© {{ date('Y') }} Aguas Santa Catalina. Todos los derechos reservados.</span>
            <span>Hecho en Chile</span>
        </div>
    </div>
</footer>

{{-- ── WhatsApp float ───────────────────────────────────────────────────────── --}}
<a href="https://wa.me/56981493272?text=Hola!%20Quiero%20hacer%20un%20pedido" target="_blank" id="wa-float" title="Chatea con nosotros">
    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
</a>

{{-- ── Cart Drawer ───────────────────────────────────────────────────────────── --}}
@include('partials.cart-drawer')

@stack('scripts')
</body>
</html>
