@extends('layouts.admin')
@section('title', 'Panel de control')
@section('page-title', 'Dashboard principal')

@section('content')

@php
    $enMantencion = filter_var($ajustes->get('mantencion_activa'), FILTER_VALIDATE_BOOLEAN);

    // Rótulos de los ejes. Se arman aquí porque el formato de fecha en español
    // lo resuelve Carbon, no el navegador.
    $etiquetas30 = [];
    for ($i = 29; $i >= 0; $i--) {
        $etiquetas30[] = now()->subDays($i)->isoFormat('D MMM');
    }
    $etiquetas7 = array_slice($etiquetas30, -7);

    /**
     * Línea de chispa para las tarjetas. Es estática: el dato ya está calculado
     * y no cambia con la interacción, así que no necesita JavaScript.
     */
    $chispa = function (array $serie, int $n = 14): string {
        $d = array_values(array_slice($serie, -$n));
        if (count($d) < 2) {
            return '';
        }
        $max   = max($d);
        $min   = min($d);
        $rango = ($max - $min) ?: 1;
        $paso  = 100 / (count($d) - 1);

        $puntos = [];
        foreach ($d as $i => $v) {
            $puntos[] = round($i * $paso, 2).','.round(29 - (($v - $min) / $rango) * 25, 2);
        }

        return implode(' ', $puntos);
    };

    $pctInventario = $stats['productos_activos'] > 0
        ? min(100, round($stats['stock_bajo'] / $stats['productos_activos'] * 100))
        : 0;
@endphp

{{-- ── Encabezado ───────────────────────────────────────────────────────── --}}
<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div class="min-w-0">
        <div class="flex flex-wrap items-center gap-3">
            <h1 class="text-[26px] font-bold text-slate-800 leading-tight" style="font-family:'Poppins',sans-serif;">Panel de Control General</h1>
            {{-- La pastilla informa de verdad: refleja si la tienda está abierta o en mantención. --}}
            <span class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-1.5 rounded-full border
                         {{ $enMantencion
                            ? 'bg-amber-50 border-amber-200 text-amber-700'
                            : 'bg-emerald-50 border-emerald-200 text-emerald-700' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $enMantencion ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                {{ $enMantencion ? 'Tienda en mantención' : 'Tienda operativa' }}
            </span>
        </div>
        <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-slate-400 mt-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            {{ ucfirst(now()->isoFormat('dddd D [de] MMMM, YYYY')) }}
            <span class="text-slate-300">•</span>
            @if($stats['ultimo_pedido'])
            <span>Último pedido {{ \Carbon\Carbon::parse($stats['ultimo_pedido'])->diffForHumans() }}</span>
            @else
            <span>Sin pedidos registrados todavía</span>
            @endif
        </p>
    </div>

    <div class="flex items-center gap-2.5 shrink-0">
        <a href="{{ route('admin.reportes.pedidos') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Exportar reporte
        </a>
        <a href="{{ route('admin.productos.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#1a56c4] text-sm font-semibold text-white hover:bg-[#0A3D7A] transition-colors shadow-sm shadow-blue-600/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nuevo producto
        </a>
    </div>
</div>

{{-- ── Tarjetas de indicadores ──────────────────────────────────────────── --}}
<div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-4">

    {{-- Facturación de hoy --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex flex-col">
        <div class="flex items-start justify-between mb-4">
            <span class="rounded-xl p-2.5 bg-sky-50 text-[#1a56c4]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            @if($stats['var_ventas'] != 0)
            <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 rounded-full border
                         {{ $stats['var_ventas'] > 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-rose-50 border-rose-200 text-rose-700' }}">
                {{ $stats['var_ventas'] > 0 ? '↑' : '↓' }} {{ abs($stats['var_ventas']) }}%
            </span>
            @else
            <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full border border-slate-200 text-slate-500">Sin cambio</span>
            @endif
        </div>

        <p class="text-[11px] font-bold uppercase tracking-[0.1em] text-slate-400">Facturación hoy</p>
        <p class="text-[26px] font-bold text-slate-800 leading-tight mt-1" style="font-family:'Poppins',sans-serif;">
            ${{ number_format($stats['ventas_hoy'], 0, ',', '.') }}<span class="text-sm font-semibold text-slate-400 ml-1">CLP</span>
        </p>
        <p class="text-xs text-slate-400 mt-1.5">
            Ayer: ${{ number_format($stats['ventas_ayer'], 0, ',', '.') }} CLP
            · {{ $stats['pedidos_hoy'] }} {{ \Illuminate\Support\Str::plural('pedido', $stats['pedidos_hoy']) }} hoy
        </p>

        <div class="h-px bg-slate-100 my-4"></div>
        <div class="flex items-end justify-between gap-3 mt-auto">
            <p class="text-xs text-slate-500 leading-snug">Últimos 14 días</p>
            @if($puntosVentas = $chispa($serieVentas))
            <svg viewBox="0 0 100 32" class="w-24 h-8 shrink-0" preserveAspectRatio="none">
                <polyline points="{{ $puntosVentas }}" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke"/>
            </svg>
            @endif
        </div>
    </div>

    {{-- Pedidos pendientes --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex flex-col">
        <div class="flex items-start justify-between mb-4">
            <span class="rounded-xl p-2.5 bg-amber-50 text-amber-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </span>
            <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full border
                         {{ $stats['pedidos_pendientes'] > 0 ? 'bg-amber-50 border-amber-200 text-amber-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700' }}">
                {{ $stats['pedidos_pendientes'] > 0 ? 'Atención prioritaria' : 'Todo al día' }}
            </span>
        </div>

        <p class="text-[11px] font-bold uppercase tracking-[0.1em] text-slate-400">Pedidos pendientes</p>
        <p class="text-[26px] font-bold text-slate-800 leading-tight mt-1" style="font-family:'Poppins',sans-serif;">
            {{ $stats['pedidos_pendientes'] }}<span class="text-sm font-semibold text-slate-400 ml-1">{{ \Illuminate\Support\Str::plural('orden', $stats['pedidos_pendientes']) }}</span>
        </p>
        <p class="text-xs text-slate-400 mt-1.5">
            {{ $stats['por_despachar'] }} {{ \Illuminate\Support\Str::plural('confirmado', $stats['por_despachar']) }} esperando salida
        </p>

        <div class="h-px bg-slate-100 my-4"></div>
        <div class="flex items-end justify-between gap-3 mt-auto">
            <a href="{{ route('admin.pedidos.index', ['estado' => 'pendiente']) }}" class="text-xs font-semibold text-[#1a56c4] hover:underline leading-snug">Revisar pendientes</a>
            @if($puntosPedidos = $chispa($seriePedidos))
            <svg viewBox="0 0 100 32" class="w-24 h-8 shrink-0" preserveAspectRatio="none">
                <polyline points="{{ $puntosPedidos }}" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke"/>
            </svg>
            @endif
        </div>
    </div>

    {{-- Unidades en ruta. En el diseño esta tarjeta decía "Bidones en tránsito ·
         98.4% retorno": el retorno de envases no se registra en ninguna parte del
         sistema, así que esa cifra queda fuera. Las unidades sí son reales: salen
         de los items de los pedidos marcados como enviados. --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex flex-col">
        <div class="flex items-start justify-between mb-4">
            <span class="rounded-xl p-2.5 bg-cyan-50 text-cyan-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m12 0h1a1 1 0 001-1v-4.586a1 1 0 00-.293-.707l-3.414-3.414A1 1 0 0015.586 7H14a1 1 0 00-1 1v8a1 1 0 001 1h1"/></svg>
            </span>
            <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full border
                         {{ $stats['pedidos_en_ruta'] > 0 ? 'bg-cyan-50 border-cyan-200 text-cyan-700' : 'border-slate-200 text-slate-500' }}">
                {{ $stats['pedidos_en_ruta'] > 0 ? 'En reparto' : 'Nada en ruta' }}
            </span>
        </div>

        <p class="text-[11px] font-bold uppercase tracking-[0.1em] text-slate-400">Unidades en ruta</p>
        <p class="text-[26px] font-bold text-slate-800 leading-tight mt-1" style="font-family:'Poppins',sans-serif;">
            {{ number_format($stats['unidades_en_ruta'], 0, ',', '.') }}<span class="text-sm font-semibold text-slate-400 ml-1">unid.</span>
        </p>
        <p class="text-xs text-slate-400 mt-1.5">
            En {{ $stats['pedidos_en_ruta'] }} {{ \Illuminate\Support\Str::plural('pedido', $stats['pedidos_en_ruta']) }} marcados como enviados
        </p>

        <div class="h-px bg-slate-100 my-4"></div>
        <div class="flex items-end justify-between gap-3 mt-auto">
            <a href="{{ route('admin.pedidos.index', ['estado' => 'enviado']) }}" class="text-xs font-semibold text-[#1a56c4] hover:underline leading-snug">Ver reparto</a>
            @if($puntosRuta = $chispa($seriePedidos))
            <svg viewBox="0 0 100 32" class="w-24 h-8 shrink-0" preserveAspectRatio="none">
                <polyline points="{{ $puntosRuta }}" fill="none" stroke="#06b6d4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke"/>
            </svg>
            @endif
        </div>
    </div>

    {{-- Nivel de inventario --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex flex-col">
        <div class="flex items-start justify-between mb-4">
            <span class="rounded-xl p-2.5 {{ $stats['stock_bajo'] > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5 19h14a2 2 0 001.84-2.75L13.74 4a2 2 0 00-3.5 0L3.16 16.25A2 2 0 005 19z"/></svg>
            </span>
            <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full border
                         {{ $stats['stock_bajo'] > 0 ? 'bg-rose-50 border-rose-200 text-rose-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700' }}">
                {{ $stats['stock_bajo'] > 0 ? 'Stock crítico' : 'Stock en orden' }}
            </span>
        </div>

        <p class="text-[11px] font-bold uppercase tracking-[0.1em] text-slate-400">Nivel de inventario</p>
        <p class="text-[26px] font-bold text-slate-800 leading-tight mt-1" style="font-family:'Poppins',sans-serif;">
            {{ $stats['stock_bajo'] }}<span class="text-sm font-semibold text-slate-400 ml-1">{{ \Illuminate\Support\Str::plural('ítem', $stats['stock_bajo']) }}</span>
        </p>
        <p class="text-xs text-slate-400 mt-1.5 truncate">
            {{ $productos_stock_bajo->pluck('nombre')->take(2)->implode(', ') ?: 'Ningún producto bajo el mínimo' }}
        </p>

        <div class="h-px bg-slate-100 my-4"></div>
        <div class="mt-auto">
            <div class="flex items-center justify-between gap-3 mb-2">
                <p class="text-xs font-semibold {{ $stats['stock_bajo'] > 0 ? 'text-rose-600' : 'text-slate-500' }} leading-snug">
                    {{ $stats['stock_bajo'] > 0 ? 'Reabastecimiento urgente' : 'Sin reposición pendiente' }}
                </p>
                <p class="text-xs text-slate-500 shrink-0 tabular-nums">{{ $pctInventario }}% del catálogo</p>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                <div class="{{ $stats['stock_bajo'] > 0 ? 'bg-rose-500' : 'bg-emerald-500' }} h-1.5 rounded-full" style="width: {{ max($pctInventario, 2) }}%"></div>
            </div>
        </div>
    </div>
</div>

{{-- ── Gráfico + pedidos recientes ──────────────────────────────────────── --}}
<div class="grid xl:grid-cols-5 gap-4 mb-4">

    {{-- Rendimiento (3/5) --}}
    <div class="xl:col-span-3 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden"
         x-data="{
            metrica: 'facturacion',
            rango: '30',
            activo: null,
            series: {
                facturacion: { '7': {{ \Illuminate\Support\Js::from(array_values(array_slice($serieVentas, -7))) }},   '30': {{ \Illuminate\Support\Js::from(array_values($serieVentas)) }},   '12': {{ \Illuminate\Support\Js::from(array_values($serieAnual)) }} },
                pedidos:     { '7': {{ \Illuminate\Support\Js::from(array_values(array_slice($seriePedidos, -7))) }},  '30': {{ \Illuminate\Support\Js::from(array_values($seriePedidos)) }},  '12': {{ \Illuminate\Support\Js::from(array_values($serieAnualPedidos)) }} },
                clientes:    { '7': {{ \Illuminate\Support\Js::from(array_values(array_slice($serieClientes, -7))) }}, '30': {{ \Illuminate\Support\Js::from(array_values($serieClientes)) }}, '12': {{ \Illuminate\Support\Js::from(array_values($serieAnualClientes)) }} }
            },
            rotulos: { '7': {{ \Illuminate\Support\Js::from($etiquetas7) }}, '30': {{ \Illuminate\Support\Js::from($etiquetas30) }}, '12': {{ \Illuminate\Support\Js::from($etiquetasMeses) }} },
            colores: { facturacion: '#1a56c4', pedidos: '#f59e0b', clientes: '#10b981' },

            get datos() { return this.series[this.metrica][this.rango] },
            get ejeX()  { return this.rotulos[this.rango] },
            get color() { return this.colores[this.metrica] },
            get tope()  { return Math.max(...this.datos, 1) },
            get suma()  { return this.datos.reduce((a, b) => a + b, 0) },
            get vacio() { return this.suma === 0 },

            formato(v) {
                const n = Math.round(v).toLocaleString('es-CL');
                return this.metrica === 'facturacion' ? '$' + n : n;
            },
            punto(i) {
                const n = this.datos.length;
                const x = n > 1 ? 52 + i * ((986 - 52) / (n - 1)) : 519;
                return [x, 246 - (this.datos[i] / this.tope) * 214];
            },
            get linea() {
                const p = this.datos.map((_, i) => this.punto(i));
                if (!p.length) return '';
                let d = 'M' + p[0][0] + ',' + p[0][1];
                for (let i = 1; i < p.length; i++) {
                    const cx = (p[i-1][0] + p[i][0]) / 2;
                    d += ' C' + cx + ',' + p[i-1][1] + ' ' + cx + ',' + p[i][1] + ' ' + p[i][0] + ',' + p[i][1];
                }
                return d;
            },
            get area() {
                const p = this.datos.map((_, i) => this.punto(i));
                if (!p.length) return '';
                return this.linea + ' L' + p[p.length-1][0] + ',246 L' + p[0][0] + ',246 Z';
            },
            get ejeVisible() {
                const paso = Math.ceil(this.ejeX.length / 5);
                return this.ejeX.filter((_, i) => i % paso === 0 || i === this.ejeX.length - 1);
            },
            rastrear(e) {
                const caja = e.currentTarget.getBoundingClientRect();
                const n = this.datos.length;
                if (n < 2) { this.activo = n ? 0 : null; return; }
                const x = ((e.clientX - caja.left) / caja.width) * 1000;
                const i = Math.round((x - 52) / ((986 - 52) / (n - 1)));
                this.activo = Math.min(n - 1, Math.max(0, i));
            }
         }">

        <div class="px-5 pt-5 pb-4 border-b border-slate-50">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                    <h2 class="font-bold text-slate-800 text-base">Rendimiento de ventas y despachos</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Ingresos, pedidos y clientes nuevos registrados en el sistema</p>
                </div>
                <span class="shrink-0 rounded-xl bg-sky-50 border border-sky-100 px-3 py-1.5 text-xs font-semibold text-[#1a56c4]">
                    {{ ucfirst(now()->isoFormat('MMMM YYYY')) }}
                </span>
            </div>

            <div class="flex flex-wrap items-center gap-2 mt-4">
                <div class="flex gap-1 bg-slate-50 p-1 rounded-xl border border-slate-100">
                    @foreach(['facturacion' => 'Facturación ($)', 'pedidos' => 'Pedidos', 'clientes' => 'Clientes nuevos'] as $clave => $etiqueta)
                    <button type="button" @click="metrica = '{{ $clave }}'; activo = null"
                            :class="metrica === '{{ $clave }}' ? 'bg-white text-slate-800 shadow-sm font-semibold' : 'text-slate-400 hover:text-slate-600'"
                            class="px-3 py-1.5 text-xs rounded-lg transition-all whitespace-nowrap">{{ $etiqueta }}</button>
                    @endforeach
                </div>

                <div class="flex gap-1 bg-slate-50 p-1 rounded-xl border border-slate-100 ml-auto">
                    @foreach(['7' => '7 días', '30' => '30 días', '12' => '12 meses'] as $clave => $etiqueta)
                    <button type="button" @click="rango = '{{ $clave }}'; activo = null"
                            :class="rango === '{{ $clave }}' ? 'bg-white text-slate-800 shadow-sm font-semibold' : 'text-slate-400 hover:text-slate-600'"
                            class="px-3 py-1.5 text-xs rounded-lg transition-all whitespace-nowrap">{{ $etiqueta }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="px-5 pt-5 pb-3 relative">
            <svg viewBox="0 0 1000 290" class="w-full block" style="height:230px" preserveAspectRatio="none"
                 @mousemove="rastrear($event)" @mouseleave="activo = null">
                <defs>
                    <linearGradient id="gradArea" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" :stop-color="color" stop-opacity="0.22"/>
                        <stop offset="100%" :stop-color="color" stop-opacity="0"/>
                    </linearGradient>
                </defs>

                {{-- Rejilla y escala vertical --}}
                <template x-for="t in [0, 0.25, 0.5, 0.75, 1]" :key="t">
                    <g>
                        <line x1="52" x2="986" :y1="32 + t * 214" :y2="32 + t * 214"
                              stroke="#eef2f7" stroke-width="1" stroke-dasharray="4 5" vector-effect="non-scaling-stroke"/>
                        <text x="0" :y="32 + t * 214 + 4" font-size="11" fill="#94a3b8" font-family="system-ui"
                              x-text="formato(tope * (1 - t))"></text>
                    </g>
                </template>

                <template x-if="!vacio">
                    <g>
                        <path :d="area" fill="url(#gradArea)"/>
                        <path :d="linea" fill="none" :stroke="color" stroke-width="2.5"
                              stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke"/>

                        {{-- Marca vertical en el punto bajo el cursor --}}
                        <template x-if="activo !== null">
                            <g>
                                <line :x1="punto(activo)[0]" :x2="punto(activo)[0]" y1="32" y2="246"
                                      :stroke="color" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.5" vector-effect="non-scaling-stroke"/>
                                <circle :cx="punto(activo)[0]" :cy="punto(activo)[1]" r="5"
                                        :fill="color" stroke="white" stroke-width="2.5" vector-effect="non-scaling-stroke"/>
                            </g>
                        </template>
                    </g>
                </template>
            </svg>

            {{-- Globo de detalle: va en HTML y no dentro del SVG para que el texto
                 no se deforme con el preserveAspectRatio del gráfico. --}}
            <template x-if="activo !== null && !vacio">
                <div class="absolute z-10 -translate-x-1/2 pointer-events-none"
                     :style="'left: calc(20px + (100% - 40px) * ' + (punto(activo)[0] / 1000) + '); top: calc(20px + 230px * ' + (punto(activo)[1] / 290) + ' - 58px)'">
                    <div class="rounded-xl bg-slate-900 text-white px-3 py-2 shadow-xl whitespace-nowrap">
                        <p class="flex items-center gap-1.5 text-[11px] text-white/60">
                            <span class="w-1.5 h-1.5 rounded-full" :style="'background:' + color"></span>
                            <span x-text="ejeX[activo]"></span>
                        </p>
                        <p class="text-sm font-bold" x-text="formato(datos[activo])"></p>
                    </div>
                </div>
            </template>

            <template x-if="vacio">
                <p class="absolute inset-x-0 top-1/2 -translate-y-1/2 text-center text-sm text-slate-400">
                    Todavía no hay datos en este período
                </p>
            </template>

            {{-- Eje horizontal, también en HTML para que no se estire --}}
            <div class="flex justify-between text-[11px] text-slate-400 mt-1" style="padding-left:52px;padding-right:4px;">
                <template x-for="(r, i) in ejeVisible" :key="i">
                    <span x-text="r"></span>
                </template>
            </div>
        </div>

        {{-- Tira de resumen del mes --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 divide-x divide-y lg:divide-y-0 divide-slate-100 border-t border-slate-100">
            @foreach([
                ['Ticket promedio', '$'.number_format($stats['ticket_promedio'], 0, ',', '.')],
                ['Ventas del mes',  '$'.number_format($stats['ventas_mes'], 0, ',', '.')],
                ['Clientes nuevos', (string) $stats['clientes_mes']],
                ['Pedidos del mes', (string) $stats['pedidos_mes']],
            ] as [$rotulo, $valor])
            <div class="px-5 py-4">
                <p class="text-xs text-slate-400">{{ $rotulo }}</p>
                <p class="text-base font-bold text-slate-800 mt-1 tabular-nums" style="font-family:'Poppins',sans-serif;">{{ $valor }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Pedidos recientes (2/5) --}}
    <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between">
            <h2 class="font-bold text-slate-800 text-base">Pedidos Recientes</h2>
            <a href="{{ route('admin.pedidos.index') }}" class="text-xs text-[#1a56c4] font-semibold hover:underline">Ver todos</a>
        </div>
        <div class="divide-y divide-slate-50 flex-1 overflow-y-auto">
            @forelse($pedidos_recientes as $pedido)
            @php
                $initials = collect(explode(' ', $pedido->nombre_cliente ?? 'C'))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join('');
                $colors = ['bg-violet-500','bg-blue-500','bg-emerald-500','bg-amber-500','bg-rose-500','bg-indigo-500','bg-teal-500','bg-orange-500'];
                $color = $colors[crc32($pedido->nombre_cliente ?? '') % count($colors)];
                $badgeMap = ['pendiente'=>['bg-amber-100 text-amber-700','Pendiente'],'confirmado'=>['bg-blue-100 text-blue-700','Confirmado'],'enviado'=>['bg-purple-100 text-purple-700','Enviado'],'entregado'=>['bg-green-100 text-green-700','Entregado'],'cancelado'=>['bg-red-100 text-red-600','Cancelado']];
                [$badge, $badgeLabel] = $badgeMap[$pedido->estado] ?? ['bg-slate-100 text-slate-600', $pedido->estado];
                $scNumber = '#SC-'.str_pad($pedido->id, 7, '0', STR_PAD_LEFT);
            @endphp
            <a href="{{ route('admin.pedidos.show', $pedido) }}"
               class="flex items-center gap-3 px-5 py-3 hover:bg-slate-50 transition-colors">
                <div class="w-9 h-9 rounded-full {{ $color }} text-white text-[11px] font-bold flex items-center justify-center shrink-0">
                    {{ $initials }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $pedido->nombre_cliente ?? 'Cliente' }}</p>
                    <p class="text-xs text-slate-400 font-mono">{{ $scNumber }}</p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-sm font-bold text-slate-800">${{ number_format($pedido->total, 0, ',', '.') }}</p>
                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full {{ $badge }}">{{ $badgeLabel }}</span>
                </div>
            </a>
            @empty
            <div class="px-5 py-8 text-center text-sm text-slate-400">Sin pedidos aún</div>
            @endforelse
        </div>
    </div>
</div>

{{-- ── Stock bajo + Métodos de pago ─────────────────────────────────── --}}
<div class="grid xl:grid-cols-5 gap-4">

    {{-- Stock bajo (3/5) --}}
    <div class="xl:col-span-3 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between">
            <div>
                <h2 class="font-bold text-slate-800 text-base">Stock Bajo</h2>
                <p class="text-xs text-slate-400">Productos que requieren reposición</p>
            </div>
            <a href="{{ route('admin.productos.index') }}?estado=activo"
               class="flex items-center gap-1.5 text-xs font-semibold text-white bg-[#0A3D7A] hover:bg-[#1a56c4] px-3 py-1.5 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Reponer Stock
            </a>
        </div>
        <div class="p-5">
            @forelse($productos_stock_bajo as $producto)
            @php
                $pct = $producto->stock_minimo > 0
                    ? min(100, round($producto->stock / $producto->stock_minimo * 100))
                    : ($producto->stock > 0 ? 100 : 0);
                $barColor = $pct <= 25 ? 'bg-rose-500' : ($pct <= 60 ? 'bg-amber-400' : 'bg-emerald-500');
                $labelColor = $pct <= 25 ? 'text-rose-600' : ($pct <= 60 ? 'text-amber-600' : 'text-emerald-600');
            @endphp
            <div class="mb-4 last:mb-0">
                <div class="flex items-center justify-between mb-1.5">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">{{ $producto->nombre }}</p>
                        <p class="text-xs text-slate-400">{{ $producto->category->nombre ?? '—' }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-bold {{ $labelColor }}">{{ $producto->stock }}</span>
                        <span class="text-xs text-slate-400"> / {{ $producto->stock_minimo }} mín</span>
                    </div>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="{{ $barColor }} h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @empty
            <div class="text-center py-6 text-sm text-slate-400">
                <svg class="w-10 h-10 text-slate-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Todo el stock está en orden
            </div>
            @endforelse
        </div>
    </div>

    {{-- Métodos de pago (2/5) --}}
    <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <h2 class="font-bold text-slate-800 text-base mb-1">Métodos de Pago</h2>
        <p class="text-xs text-slate-400 mb-5">Distribución de este mes</p>

        @php
            $totalPedidos = $mediosPago->sum('pedidos');
            $paleta = [
                'webpay'         => ['#1a56c4', 'Webpay / Transbank'],
                'transferencia'  => ['#10b981', 'Transferencia'],
                'whatsapp'       => ['#f59e0b', 'WhatsApp'],
                'mercadopago'    => ['#6366f1', 'MercadoPago'],
                'contra_entrega' => ['#64748b', 'Contra entrega'],
            ];
            $circunferencia = 2 * M_PI * 48;
            $acumulado = 0;
        @endphp

        @if($totalPedidos === 0)
        <div class="flex flex-col items-center justify-center py-10 text-center">
            <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <p class="text-sm font-semibold text-slate-500">Aún no hay pagos este mes</p>
            <p class="text-xs text-slate-400 mt-1">El desglose aparece con el primer pedido</p>
        </div>
        @else
        <div class="flex items-center justify-center mb-5">
            <svg viewBox="0 0 120 120" class="w-36 h-36">
                <circle cx="60" cy="60" r="48" fill="none" stroke="#e2e8f0" stroke-width="18"/>
                @foreach($mediosPago as $medio)
                    @php
                        $fraccion = $medio->pedidos / $totalPedidos;
                        $largo    = $fraccion * $circunferencia;
                        $color    = $paleta[$medio->metodo_pago][0] ?? '#94a3b8';
                    @endphp
                    <circle cx="60" cy="60" r="48" fill="none" stroke="{{ $color }}" stroke-width="18"
                        stroke-dasharray="{{ round($largo, 2) }} {{ round($circunferencia - $largo, 2) }}"
                        stroke-dashoffset="{{ round(-$acumulado, 2) }}" transform="rotate(-90 60 60)"/>
                    @php $acumulado += $largo; @endphp
                @endforeach
                @php $principal = $mediosPago->first(); @endphp
                <text x="60" y="56" text-anchor="middle" font-size="14" font-weight="700" fill="#1e293b" font-family="system-ui">{{ round(($principal->pedidos / $totalPedidos) * 100) }}%</text>
                <text x="60" y="70" text-anchor="middle" font-size="7" fill="#94a3b8" font-family="system-ui">{{ $paleta[$principal->metodo_pago][1] ?? $principal->metodo_pago }}</text>
            </svg>
        </div>
        <div class="space-y-3">
            @foreach($mediosPago as $medio)
            @php [$color, $etiqueta] = $paleta[$medio->metodo_pago] ?? ['#94a3b8', ucfirst($medio->metodo_pago)]; @endphp
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $color }}"></span>
                    <span class="text-sm text-slate-600 truncate">{{ $etiqueta }}</span>
                </div>
                <div class="text-right shrink-0 ml-2">
                    <span class="text-sm font-bold text-slate-800">{{ round(($medio->pedidos / $totalPedidos) * 100) }}%</span>
                    <span class="text-xs text-slate-400 ml-1">({{ $medio->pedidos }})</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@endsection
