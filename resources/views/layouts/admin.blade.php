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

@php
    // Los contadores alimentan las insignias de la barra y la campana. Son dos
    // consultas por página del panel; si algún día pesan, van a caché corta.
    $stockBajoCount         = \App\Models\Product::whereColumn('stock', '<=', 'stock_minimo')->where('activo', true)->count();
    $pedidosPendientesCount = \App\Models\Order::where('estado', 'pendiente')->count();
    $enMantencion           = filter_var($ajustes->get('mantencion_activa'), FILTER_VALIDATE_BOOLEAN);

    $navGrupos = [
        [
            'titulo' => 'Menú principal',
            'items'  => [
                ['route' => 'admin.dashboard', 'label' => 'Dashboard',
                 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],

                ['route' => 'admin.pedidos.index', 'label' => 'Pedidos y despacho',
                 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                 'badge' => $pedidosPendientesCount > 0 ? $pedidosPendientesCount.' '.\Illuminate\Support\Str::plural('orden', $pedidosPendientesCount) : null,
                 'badge_clase' => 'bg-amber-400/15 text-amber-200 border-amber-300/25'],

                ['route' => 'admin.productos.index', 'label' => 'Inventario y botellones',
                 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                 'badge' => $stockBajoCount > 0 ? $stockBajoCount.' crítico' : null,
                 'badge_clase' => 'bg-rose-500/15 text-rose-200 border-rose-400/25'],

                ['route' => 'admin.clientes.index', 'label' => 'Clientes',
                 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ],
        ],
        [
            'titulo' => 'Marketing y tienda',
            'items'  => [
                ['route' => 'admin.banners.index', 'label' => 'Banners y promociones',
                 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['route' => 'admin.cupones.index', 'label' => 'Cupones de descuento',
                 'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z'],
                ['route' => 'admin.paginas.index', 'label' => 'Páginas institucionales',
                 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['route' => 'admin.menus.index', 'label' => 'Menús de navegación',
                 'icon' => 'M4 6h16M4 12h16M4 18h7'],
            ],
        ],
        [
            'titulo' => 'Sistema y métricas',
            'items'  => [
                ['route' => 'admin.estadisticas', 'label' => 'Estadísticas de la web',
                 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ['route' => 'admin.sistema', 'label' => 'Estado del sistema',
                 'icon' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-3-4h.01M17 16h.01'],
                ['route' => 'admin.configuracion', 'label' => 'Configuración',
                 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
            ],
        ],
    ];
@endphp

<div class="flex h-screen overflow-hidden">

    {{-- ── SIDEBAR ───────────────────────────────────────────────────── --}}
    <aside id="admin-sidebar"
           style="background:linear-gradient(180deg,#0E4285 0%,#0A2F5E 48%,#071F42 100%);"
           class="fixed left-0 top-0 h-full w-[272px] text-white z-50 flex flex-col shadow-2xl transition-transform duration-300 select-none
                  lg:static lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        {{-- Marca --}}
        <div class="px-4 pt-4 pb-5 flex items-center gap-3">
            <span class="shrink-0 bg-white rounded-xl p-2 shadow-lg shadow-black/20">
                <img src="{{ asset('images/logo.png') }}" alt="" class="h-9 w-auto block">
            </span>
            <div class="min-w-0">
                <p class="flex items-center gap-1.5 text-[13px] font-bold text-white uppercase tracking-wide leading-tight truncate"
                   style="font-family:'Poppins',sans-serif;">
                    Santa Catalina
                    {{-- El punto sí informa: verde con la tienda abierta, ámbar en mantención. --}}
                    <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $enMantencion ? 'bg-amber-400' : 'bg-emerald-400' }}"
                          title="{{ $enMantencion ? 'Tienda en mantención' : 'Tienda operativa' }}"></span>
                </p>
                <p class="text-[10px] font-semibold text-sky-200/60 uppercase tracking-[0.14em] mt-0.5">Panel de administración</p>
            </div>
        </div>

        {{-- Navegación --}}
        <nav class="flex-1 overflow-y-auto px-3 pb-3 space-y-5">
            @foreach($navGrupos as $grupo)
            <div class="space-y-1">
                <p class="px-3 pb-1 text-[10px] font-bold text-white/35 uppercase tracking-[0.12em]">{{ $grupo['titulo'] }}</p>

                @foreach($grupo['items'] as $item)
                @php
                    $activo = request()->routeIs(rtrim($item['route'], '.index').'*');
                    $existe = \Illuminate\Support\Facades\Route::has($item['route']);
                @endphp
                <a href="{{ $existe ? route($item['route']) : '#' }}"
                   class="group flex items-center gap-3 pl-2 pr-3 py-2 rounded-xl transition-colors text-sm
                          {{ $activo
                             ? 'bg-white/12 border border-white/10 text-white font-semibold'
                             : 'border border-transparent text-white/65 font-medium hover:bg-white/8 hover:text-white' }}">
                    <span class="shrink-0 rounded-lg p-1.5 transition-colors
                                 {{ $activo ? 'bg-white text-[#0A3D7A]' : 'text-white/45 group-hover:text-white/80' }}">
                        <svg class="w-4.5 h-4.5 block" style="width:18px;height:18px;" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                        </svg>
                    </span>
                    <span class="truncate">{{ $item['label'] }}</span>

                    @if(!empty($item['badge']))
                    <span class="ml-auto shrink-0 text-[10px] font-semibold px-2 py-0.5 rounded-full border {{ $item['badge_clase'] }}">{{ $item['badge'] }}</span>
                    @elseif($activo)
                    <span class="ml-auto shrink-0 w-1.5 h-1.5 rounded-full bg-sky-300"></span>
                    @endif
                </a>
                @endforeach
            </div>
            @endforeach
        </nav>

        {{-- Aquí había una tarjeta de WhatsApp con el número de la propia tienda:
             el botón abría una conversación del administrador consigo mismo. --}}
        <a href="{{ route('home') }}" target="_blank" rel="noopener"
           class="flex items-center gap-3 px-3.5 py-3 m-3 rounded-2xl bg-white/8 border border-white/10 text-sm font-semibold text-white/85 hover:bg-white/12 hover:text-white transition-colors">
            <svg class="w-4 h-4 shrink-0 text-white/60" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l2-5h14l2 5M3 9h18v10a1 1 0 01-1 1H4a1 1 0 01-1-1V9zm5 0v3a4 4 0 008 0V9"/>
            </svg>
            Ver tienda online
            <span class="ml-auto shrink-0 rounded-lg bg-white/10 p-1.5">
                <svg class="w-3.5 h-3.5 block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </span>
        </a>
    </aside>

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen=false"
         class="fixed inset-0 z-40 bg-black/50 lg:hidden"></div>

    {{-- ── MAIN ──────────────────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">

        {{-- Barra superior --}}
        <header class="bg-white border-b border-slate-200 px-4 lg:px-6 py-3 flex items-center gap-3 shrink-0">
            <button @click="sidebarOpen=true" class="lg:hidden p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 transition-colors" aria-label="Abrir menú">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Ruta de migas: la sección actual va en pastilla, como el resto de los controles --}}
            <nav class="hidden sm:flex items-center gap-2 shrink-0" aria-label="Ubicación">
                <a href="{{ route('admin.dashboard') }}" class="text-[13px] font-medium text-slate-400 hover:text-slate-700 transition-colors">Panel</a>
                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                <span class="rounded-xl bg-sky-50 border border-sky-100 px-3 py-1.5 text-[13px] font-semibold text-[#1a56c4]">@yield('page-title', 'Dashboard')</span>
            </nav>

            {{-- Buscador: apunta a pedidos, que es lo que uno busca de verdad desde
                 el panel (un cliente, un correo, un número de pedido). --}}
            <form method="GET" action="{{ route('admin.pedidos.index') }}"
                  class="flex-1 min-w-0 max-w-sm"
                  x-data
                  @keydown.window="if (($event.key === 'k' || $event.key === 'K') && ($event.metaKey || $event.ctrlKey)) { $event.preventDefault(); $refs.buscar.focus(); }">
                <div class="relative">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input x-ref="buscar" type="search" name="q" value="{{ request('q') }}"
                           placeholder="Buscar pedido, cliente o email…"
                           class="w-full pl-9 pr-14 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl outline-none
                                  focus:border-[#1a56c4] focus:bg-white focus:ring-2 focus:ring-[#1a56c4]/15 transition">
                    <kbd class="hidden md:block absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] font-sans font-semibold text-slate-400 bg-white border border-slate-200 rounded-md px-1.5 py-0.5">Ctrl K</kbd>
                </div>
            </form>

            <div class="ml-auto flex items-center gap-2 sm:gap-3 shrink-0">

                {{-- Horario de atención: dato real de Configuración. Reemplaza al
                     "Turno Mañana & Tarde" del diseño, que no salía de ningún lado. --}}
                <span class="hidden xl:block rounded-xl border border-slate-200 px-3 py-1.5 text-[13px] text-slate-500">
                    Atención: <span class="font-semibold text-slate-700">{{ $ajustes->get('horario') }}</span>
                </span>

                {{-- Campana: lleva a los pedidos pendientes y sólo se enciende si hay --}}
                <a href="{{ route('admin.pedidos.index', ['estado' => 'pendiente']) }}"
                   class="relative p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors"
                   title="{{ $pedidosPendientesCount > 0 ? $pedidosPendientesCount.' pedido(s) pendiente(s)' : 'Sin pedidos pendientes' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1h6z"/>
                    </svg>
                    @if($pedidosPendientesCount > 0)
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white"></span>
                    @endif
                </a>

                <div class="w-px h-7 bg-slate-200 hidden sm:block"></div>

                {{-- Usuario --}}
                <div class="relative" x-data="{ menu: false }" @click.outside="menu = false">
                    <button @click="menu = !menu"
                            class="flex items-center gap-2.5 p-1 pr-2 rounded-xl hover:bg-slate-100 transition-colors"
                            :aria-expanded="menu" aria-haspopup="true">
                        <span class="w-9 h-9 rounded-xl bg-[#0A3D7A] flex items-center justify-center text-white text-xs font-bold shrink-0">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(auth()->user()->nombre ?? 'A', 0, 2)) }}
                        </span>
                        <span class="hidden sm:block text-left leading-tight">
                            <span class="block text-[13px] font-semibold text-slate-800 truncate max-w-[140px]">{{ auth()->user()->nombre ?? 'Administrador' }}</span>
                            <span class="block text-[11px] text-slate-400">Administrador</span>
                        </span>
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="menu" x-cloak x-transition.origin.top.right
                         class="absolute right-0 mt-2 w-56 rounded-2xl bg-white border border-slate-200 shadow-xl shadow-slate-900/10 p-1.5 z-50">
                        <p class="px-3 py-2 text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                        <a href="{{ route('home') }}" target="_blank" rel="noopener"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Ver tienda online
                        </a>
                        <a href="{{ route('admin.configuracion') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Configuración
                        </a>
                        <div class="h-px bg-slate-100 my-1.5 mx-2"></div>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm text-rose-600 hover:bg-rose-50 transition-colors text-left">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
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
