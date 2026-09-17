<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Santa Catalina</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <script>window.TIENDA = @json($ajustes->publicos());</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F1F5F9] text-slate-800 font-sans antialiased" x-data="{ sidebarOpen: false }">

<div class="flex h-screen overflow-hidden">

    {{-- ── SIDEBAR ───────────────────────────────────────────────────── --}}
    <aside id="admin-sidebar"
           class="fixed left-0 top-0 h-full w-[260px] bg-[#0A3D7A] text-white z-50 flex flex-col border-r border-white/10 shadow-2xl transition-transform duration-300 select-none
                  lg:static lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        {{-- Brand --}}
        <div class="p-5 flex items-center gap-3 border-b border-white/10">
            <img src="{{ asset('images/isotipo-blanco.png') }}" alt="" class="h-11 w-auto shrink-0">
            <div>
                <p class="font-bold text-[17px] leading-tight text-white tracking-tight" style="font-family:'Poppins',sans-serif;">Santa Catalina</p>
                <p class="text-[11px] text-sky-200/80 font-medium tracking-wide uppercase">Panel de administración</p>
            </div>
        </div>

        {{-- Nav --}}
        <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            <p class="px-3 py-1 text-[11px] font-semibold text-white/40 uppercase tracking-wider">Menú Principal</p>

            @php
                $stockBajoCount = $stockBajoCount ?? \App\Models\Product::whereColumn('stock', '<=', 'stock_minimo')->where('activo', true)->count();
                $pedidosPendientesCount = $pedidosPendientesCount ?? \App\Models\Order::where('estado', 'pendiente')->count();
                $navMain = [
                    ['route' => 'admin.dashboard',       'label' => 'Dashboard',    'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'badge' => null],
                    ['route' => 'admin.productos.index', 'label' => 'Productos',    'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'badge' => $stockBajoCount > 0 ? $stockBajoCount.' bajo' : null, 'badge_color' => 'bg-rose-500/80 text-white'],
                    ['route' => 'admin.pedidos.index',   'label' => 'Pedidos',      'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'badge' => $pedidosPendientesCount > 0 ? $pedidosPendientesCount : null, 'badge_color' => 'bg-amber-400 text-slate-900 font-bold'],
                    ['route' => 'admin.clientes.index',  'label' => 'Clientes',     'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'badge' => null],
                    ['route' => 'admin.banners.index',   'label' => 'Banners',      'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'badge' => null],
                    ['route' => 'admin.cupones.index',   'label' => 'Cupones',      'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z', 'badge' => null],
                ];
                $navSitio = [
                    ['route' => 'admin.paginas.index', 'label' => 'Páginas', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['route' => 'admin.menus.index',   'label' => 'Menús',   'icon' => 'M4 6h16M4 12h16M4 18h7'],
                ];
            @endphp

            @foreach($navMain as $item)
            @php
                $active = request()->routeIs(rtrim($item['route'], '.index').'*');
                $routeExists = \Illuminate\Support\Facades\Route::has($item['route']);
                $href = $routeExists ? route($item['route']) : '#';
            @endphp
            <a href="{{ $href }}"
               class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all text-sm font-medium
                      {{ $active ? 'bg-white/15 text-white font-semibold shadow-sm border border-white/10' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0 {{ $active ? 'text-sky-300' : 'text-white/60' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                    </svg>
                    <span>{{ $item['label'] }}</span>
                </div>
                @if(!empty($item['badge']))
                <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ $item['badge_color'] ?? 'bg-white/20 text-white' }}">{{ $item['badge'] }}</span>
                @endif
            </a>
            @endforeach

            <div class="h-px bg-white/10 my-3 mx-2"></div>
            <p class="px-3 py-1 text-[11px] font-semibold text-white/40 uppercase tracking-wider">Contenido del Sitio</p>

            @foreach($navSitio as $item)
            @php
                $activoSitio = request()->routeIs(rtrim($item['route'], '.index').'*');
                $hrefSitio   = \Illuminate\Support\Facades\Route::has($item['route']) ? route($item['route']) : '#';
            @endphp
            <a href="{{ $hrefSitio }}"
               class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all text-sm font-medium
                      {{ $activoSitio ? 'bg-white/15 text-white font-semibold shadow-sm border border-white/10' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0 {{ $activoSitio ? 'text-sky-300' : 'text-white/60' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                </svg>
                <span>{{ $item['label'] }}</span>
            </a>
            @endforeach

            <div class="h-px bg-white/10 my-3 mx-2"></div>
            <p class="px-3 py-1 text-[11px] font-semibold text-white/40 uppercase tracking-wider">Análisis y Ajustes</p>

            @foreach([['admin.estadisticas', 'Estadísticas', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'], ['admin.sistema', 'Sistema', 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-3-4h.01M17 16h.01'], ['admin.configuracion', 'Configuración', 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z']] as [$r,$l,$ico])
            @php $active2 = request()->routeIs($r.'*'); $exists2 = \Illuminate\Support\Facades\Route::has($r); @endphp
            <a href="{{ $exists2 ? route($r) : '#' }}"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all
                      {{ $active2 ? 'bg-white/15 text-white border border-white/10' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0 {{ $active2 ? 'text-sky-300' : 'text-white/60' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico }}"/>
                </svg>
                {{ $l }}
            </a>
            @endforeach
        </div>

        {{-- Aquí había una tarjeta de WhatsApp con el número de la propia
             tienda: el botón "Contactar" abría una conversación del
             administrador consigo mismo, y el punto verde que parpadeaba no
             consultaba ningún estado. En su lugar va lo que sí faltaba, que
             el panel no tenía en ninguna parte: salir a ver la tienda. --}}
        <a href="{{ route('home') }}" target="_blank" rel="noopener"
           class="flex items-center gap-2.5 px-3.5 py-3 m-3 rounded-2xl bg-white/5 border border-white/10 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white transition-colors">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l2-5h14l2 5M3 9h18v10a1 1 0 01-1 1H4a1 1 0 01-1-1V9zm5 0v3a4 4 0 008 0V9"/>
            </svg>
            Ver la tienda
            <svg class="w-3.5 h-3.5 ml-auto shrink-0 text-white/40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
        </a>
    </aside>

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen=false"
         class="fixed inset-0 z-40 bg-black/50 lg:hidden"></div>

    {{-- ── MAIN ──────────────────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">

        {{-- Header --}}
        <header class="bg-white border-b border-slate-200 px-4 lg:px-6 py-3 flex items-center gap-4 shrink-0 shadow-xs">
            <button @click="sidebarOpen=true" class="lg:hidden p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-1.5 text-xs text-slate-400 font-medium">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-700 transition-colors">Panel</a>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-slate-700">@yield('page-title', 'Dashboard')</span>
            </nav>

            {{-- Search --}}
            <div class="flex-1 hidden md:block max-w-xs ml-2">
                <div class="relative">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" placeholder="Buscar en dashboard..."
                           class="w-full pl-9 pr-3 py-1.5 text-sm bg-slate-50 border border-slate-200 rounded-lg outline-none focus:border-[#1a56c4] focus:bg-white transition-colors">
                </div>
            </div>

            <div class="ml-auto flex items-center gap-3">
                {{-- User avatar --}}
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-[#1a56c4] flex items-center justify-center text-white text-xs font-bold shrink-0">
                        {{ strtoupper(substr(auth()->user()->nombre ?? 'A', 0, 1)) }}
                    </div>
                    <span class="hidden sm:block text-sm font-semibold text-slate-700 truncate max-w-[120px]">{{ auth()->user()->nombre ?? 'Admin' }}</span>
                </div>
                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors" title="Cerrar sesión">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </header>

        {{-- Flash --}}
        @if(session('success'))
        <div class="mx-4 lg:mx-6 mt-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm font-medium shadow-xs">
            <svg class="w-4 h-4 shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mx-4 lg:mx-6 mt-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm font-medium shadow-xs">
            <svg class="w-4 h-4 shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
        @endif

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto p-4 lg:p-6">
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>
