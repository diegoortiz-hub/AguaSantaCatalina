@extends('layouts.app')
@section('title', $currentCategory ? $currentCategory->nombre : 'Catálogo de Productos')

@section('content')

{{-- Breadcrumb --}}
<div class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-[#0A3D7A] transition">Inicio</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('productos.index') }}" class="hover:text-[#0A3D7A] transition">Productos</a>
        @if($currentCategory)
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-[#0A3D7A] font-medium">{{ $currentCategory->nombre }}</span>
        @endif
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-8"
     x-data="{
        mobileFilters: false,
        sort: '{{ request('sort','newest') }}',
        minPrice: {{ (int) request('precio_min', 0) }},
        maxPrice: {{ (int) request('precio_max', $maxPriceInDb) }},
        maxPriceInDb: {{ $maxPriceInDb }},
        minPct() { return Math.round((this.minPrice / this.maxPriceInDb) * 100); },
        maxPct() { return Math.round((this.maxPrice / this.maxPriceInDb) * 100); },
        applySort() {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', this.sort);
            url.searchParams.set('page', 1);
            window.location = url.toString();
        },
        submitFilters() {
            document.getElementById('filter-form').submit();
        }
     }">

    {{-- ── Header Row ─────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold section-title">
                {{ $currentCategory ? $currentCategory->nombre : 'Todos los Productos' }}
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $products->total() }} producto{{ $products->total() !== 1 ? 's' : '' }} encontrado{{ $products->total() !== 1 ? 's' : '' }}</p>
        </div>

        <div class="flex items-center gap-2">
            {{-- Mobile filter button --}}
            <button @click="mobileFilters = true"
                    class="lg:hidden flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filtros
                @if(request()->hasAny(['precio_min','precio_max','solo_stock','destacado']))
                <span class="w-2 h-2 rounded-full bg-cyan-600"></span>
                @endif
            </button>

            {{-- Sort --}}
            <select x-model="sort" @change="applySort()"
                    class="form-input w-auto text-sm py-2 pr-8">
                <option value="newest">Más recientes</option>
                <option value="destacado">Destacados</option>
                <option value="precio_asc">Precio: menor → mayor</option>
                <option value="precio_desc">Precio: mayor → menor</option>
                <option value="nombre">Nombre A–Z</option>
            </select>
        </div>
    </div>

    <div class="flex gap-6">

        {{-- ── SIDEBAR (desktop) ───────────────────────────────────────────────── --}}
        <aside class="hidden lg:block w-60 shrink-0">
            @include('partials.catalog-filters', ['isMobile' => false])
        </aside>

        {{-- ── PRODUCTS GRID ─────────────────────────────────────────────────── --}}
        <div class="flex-1 min-w-0">

            {{-- Active filter chips --}}
            @if(request()->hasAny(['q','categoria','precio_min','precio_max','solo_stock','destacado']))
            <div class="flex flex-wrap gap-2 mb-4">
                @if(request('q'))
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-cyan-100 text-cyan-800 text-xs font-medium">
                    Búsqueda: "{{ request('q') }}"
                    <a href="{{ route('productos.index', request()->except('q')) }}" class="ml-1 hover:text-cyan-900">✕</a>
                </span>
                @endif
                @if(request('categoria') && $currentCategory)
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-medium">
                    {{ $currentCategory->nombre }}
                    <a href="{{ route('productos.index', request()->except('categoria')) }}" class="ml-1 hover:text-blue-900">✕</a>
                </span>
                @endif
                @if(request('precio_min') || request('precio_max'))
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-medium">
                    ${{ number_format(request('precio_min', 0), 0, ',', '.') }} – ${{ number_format(request('precio_max', $maxPriceInDb), 0, ',', '.') }}
                    <a href="{{ route('productos.index', request()->except(['precio_min','precio_max'])) }}" class="ml-1 hover:text-slate-900">✕</a>
                </span>
                @endif
                @if(request('solo_stock'))
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-medium">
                    Solo con stock
                    <a href="{{ route('productos.index', request()->except('solo_stock')) }}" class="ml-1 hover:text-emerald-900">✕</a>
                </span>
                @endif
                <a href="{{ route('productos.index') }}" class="text-xs text-rose-500 hover:text-rose-700 font-medium self-center ml-1">Limpiar todos</a>
            </div>
            @endif

            @forelse($products as $p)
            @if($loop->first)
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @endif

            <div class="card flex flex-col group" x-data>
                <div class="relative overflow-hidden" style="background:linear-gradient(135deg,#EFF6FF,#DBEAFE);height:180px;display:flex;align-items:center;justify-content:center;">
                    @if($p->badge)
                    <span class="absolute top-2 left-2 badge badge-{{ $p->badge_color ?? 'blue' }}">{{ $p->badge }}</span>
                    @endif
                    @if($p->tieneDescuento())
                    <span class="absolute top-2 right-2 badge badge-red">-{{ $p->porcentajeDescuento() }}%</span>
                    @endif
                    @if($p->stock === 0)
                    <div class="absolute inset-0 bg-white/60 flex items-center justify-center">
                        <span class="badge badge-gray">Sin Stock</span>
                    </div>
                    @endif
                    @if($p->imagen)
                    <img src="{{ Storage::url($p->imagen) }}" alt="{{ $p->nombre }}"
                         class="max-h-full max-w-full object-contain p-3 group-hover:scale-105 transition-transform duration-300">
                    @else
                    <div class="group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-16 h-16 text-[#1a56c4]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    @endif
                    @if($p->stock > 0)
                    <button @click="$store.cart.add({{ $p->id }}, '{{ addslashes($p->nombre) }}', {{ $p->precio }})"
                        class="absolute bottom-2 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity btn-primary text-xs px-4 py-1.5 whitespace-nowrap shadow">
                        + Agregar
                    </button>
                    @endif
                </div>
                <div class="p-4 flex flex-col flex-1">
                    <p class="text-[10px] text-[#1a56c4] font-medium mb-0.5 uppercase tracking-wide">{{ $p->category->nombre }}</p>
                    <a href="{{ route('productos.show', $p->slug) }}"
                       class="text-sm font-semibold text-gray-800 hover:text-[#0A3D7A] leading-snug mb-2 flex-1 line-clamp-2"
                       style="font-family:'Poppins',sans-serif;">{{ $p->nombre }}</a>
                    <div class="stars text-xs mb-2">★★★★★ <span class="text-gray-400 text-xs" style="font-family:'Inter',sans-serif;">({{ rand(8,120) }})</span></div>

                    @if($p->stock > 0 && $p->stock <= $p->stock_minimo)
                    <span class="text-xs text-orange-600 font-medium mb-1">⚠ Solo {{ $p->stock }} disponibles</span>
                    @elseif($p->stock === 0)
                    <span class="text-xs text-red-500 font-medium mb-1">Agotado</span>
                    @endif

                    <div class="flex items-baseline gap-2 mb-3">
                        <span class="text-xl font-black text-[#0A3D7A]" style="font-family:'Poppins',sans-serif;">${{ number_format($p->precio, 0, ',', '.') }}</span>
                        @if($p->precio_original)
                        <span class="text-xs text-gray-400 line-through">${{ number_format($p->precio_original, 0, ',', '.') }}</span>
                        @endif
                    </div>

                    <div class="flex gap-2 mt-auto">
                        <a href="{{ route('productos.show', $p->slug) }}"
                           class="flex-1 text-center py-2 text-xs font-semibold border-2 border-[#0A3D7A] text-[#0A3D7A] rounded-lg hover:bg-[#0A3D7A] hover:text-white transition">
                            Ver
                        </a>
                        @if($p->stock > 0)
                        <button @click="$store.cart.add({{ $p->id }}, '{{ addslashes($p->nombre) }}', {{ $p->precio }})"
                            class="flex-1 btn-primary text-xs py-2 justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Carrito
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            @if($loop->last)
            </div>
            @endif

            @empty
            <div class="text-center py-20">
                <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-600 mb-2" style="font-family:'Poppins',sans-serif;">Sin resultados</h3>
                <p class="text-gray-400 mb-6">No encontramos productos con esos filtros.</p>
                <a href="{{ route('productos.index') }}" class="btn-primary">Ver todos los productos</a>
            </div>
            @endforelse

            {{-- Pagination --}}
            @if($products->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $products->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- ── Mobile Filters Drawer ──────────────────────────────────────────────── --}}
    <div x-show="mobileFilters" x-cloak class="fixed inset-0 z-50 lg:hidden">
        <div class="absolute inset-0 bg-black/50" @click="mobileFilters = false"></div>
        <div class="absolute inset-y-0 left-0 w-80 bg-white shadow-xl overflow-y-auto p-5">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-slate-800">Filtros</h3>
                <button @click="mobileFilters = false" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            @include('partials.catalog-filters', ['isMobile' => true])
        </div>
    </div>

</div>
@endsection
