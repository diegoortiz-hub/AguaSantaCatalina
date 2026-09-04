@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- ── Page header ───────────────────────────────────────────────────── --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800" style="font-family:'Poppins',sans-serif;">Panel de Control</h1>
        <p class="text-sm text-slate-400 mt-0.5">{{ now()->isoFormat('dddd D [de] MMMM, YYYY') }}</p>
    </div>
    <a href="{{ route('admin.pedidos.index') }}"
       class="hidden sm:flex items-center gap-2 px-4 py-2.5 bg-[#0A3D7A] text-white text-sm font-semibold rounded-xl hover:bg-[#1a56c4] transition-colors shadow-md">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Ver Pedidos
    </a>
</div>

{{-- ── KPI Cards ─────────────────────────────────────────────────────── --}}
@php
    $var_v = $stats['var_ventas'];
    $var_c = $stats['var_clientes'];
@endphp
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

    {{-- Ventas del día --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-white pointer-events-none"></div>
        <div class="relative">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $var_v >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $var_v >= 0 ? '+' : '' }}{{ $var_v }}%
                </span>
            </div>
            <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Ventas del día</p>
            <p class="text-2xl font-bold text-slate-900 mt-0.5">${{ number_format($stats['ventas_hoy'], 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-1">vs ayer ${{ number_format($stats['ventas_ayer'], 0, ',', '.') }}</p>
            {{-- Sparkline --}}
            <svg viewBox="0 0 80 24" class="w-full h-8 mt-3 text-blue-400" preserveAspectRatio="none">
                <polyline fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    points="0,18 10,14 20,20 30,8 40,12 50,6 60,10 70,4 80,8"/>
            </svg>
        </div>
    </div>

    {{-- Pedidos pendientes --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-amber-50/50 to-white pointer-events-none"></div>
        <div class="relative">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                @if($stats['pedidos_pendientes'] > 0)
                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 animate-pulse">Urgente</span>
                @else
                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-green-100 text-green-700">Al día</span>
                @endif
            </div>
            <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Pedidos pendientes</p>
            <p class="text-2xl font-bold text-slate-900 mt-0.5">{{ $stats['pedidos_pendientes'] }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $stats['pedidos_mes'] }} totales este mes</p>
            <svg viewBox="0 0 80 24" class="w-full h-8 mt-3 text-amber-400" preserveAspectRatio="none">
                <polyline fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    points="0,10 10,14 20,8 30,18 40,6 50,12 60,16 70,10 80,14"/>
            </svg>
        </div>
    </div>

    {{-- Clientes nuevos --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-50/50 to-white pointer-events-none"></div>
        <div class="relative">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $var_c >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $var_c >= 0 ? '+' : '' }}{{ $var_c }}%
                </span>
            </div>
            <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Clientes nuevos</p>
            <p class="text-2xl font-bold text-slate-900 mt-0.5">{{ $stats['clientes_mes'] }}</p>
            <p class="text-xs text-slate-400 mt-1">este mes</p>
            <svg viewBox="0 0 80 24" class="w-full h-8 mt-3 text-emerald-400" preserveAspectRatio="none">
                <polyline fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    points="0,20 10,16 20,18 30,12 40,10 50,8 60,6 70,4 80,2"/>
            </svg>
        </div>
    </div>

    {{-- Stock bajo --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-rose-50/50 to-white pointer-events-none"></div>
        <div class="relative">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                @if($stats['stock_bajo'] > 0)
                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-700">Action Required</span>
                @else
                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-green-100 text-green-700">OK</span>
                @endif
            </div>
            <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">Stock bajo</p>
            <p class="text-2xl font-bold text-slate-900 mt-0.5">{{ $stats['stock_bajo'] }}</p>
            <p class="text-xs text-slate-400 mt-1">productos requieren reposición</p>
            <svg viewBox="0 0 80 24" class="w-full h-8 mt-3 text-rose-400" preserveAspectRatio="none">
                <polyline fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    points="0,4 10,8 20,6 30,14 40,10 50,18 60,14 70,20 80,18"/>
            </svg>
        </div>
    </div>
</div>

{{-- ── Chart + Recent Orders ─────────────────────────────────────────── --}}
<div class="grid xl:grid-cols-5 gap-4 mb-4">

    {{-- Sales chart (3/5 width) --}}
    <div class="xl:col-span-3 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden"
         x-data="{
            activePeriod: 'mes',
            chartData: {
                hoy:    @json(array_values(array_slice($chartData, -1))),
                semana: @json(array_values(array_slice($chartData, -7))),
                mes:    @json(array_values($chartData)),
                año:    @json(array_values($chartData))
            },
            get currentData() { return this.chartData[this.activePeriod] },
            get maxVal() { return Math.max(...this.currentData, 1) },
            get minVal() { return Math.min(...this.currentData, 0) },
            svgPath() {
                const d = this.currentData;
                if (!d.length) return '';
                const w = 800, h = 200, pad = 10;
                const xStep = d.length > 1 ? (w - pad*2) / (d.length - 1) : w;
                const range = this.maxVal - this.minVal || 1;
                const pts = d.map((v, i) => {
                    const x = pad + i * xStep;
                    const y = pad + (1 - (v - this.minVal) / range) * (h - pad*2);
                    return [x, y];
                });
                return pts.map((p, i) => (i === 0 ? 'M' : 'L') + p[0] + ',' + p[1]).join(' ');
            },
            areaPath() {
                const d = this.currentData;
                if (!d.length) return '';
                const w = 800, h = 200, pad = 10;
                const xStep = d.length > 1 ? (w - pad*2) / (d.length - 1) : w;
                const range = this.maxVal - this.minVal || 1;
                const pts = d.map((v, i) => {
                    const x = pad + i * xStep;
                    const y = pad + (1 - (v - this.minVal) / range) * (h - pad*2);
                    return [x, y];
                });
                const line = pts.map((p, i) => (i === 0 ? 'M' : 'L') + p[0] + ',' + p[1]).join(' ');
                const lastX = pts[pts.length - 1][0];
                const firstX = pts[0][0];
                return line + ` L${lastX},${h} L${firstX},${h} Z`;
            }
         }">
        <div class="px-5 pt-5 pb-3 flex items-center justify-between border-b border-slate-50">
            <div>
                <h2 class="font-bold text-slate-800 text-base">Evolución de Ventas</h2>
                <p class="text-xs text-slate-400 mt-0.5">${{ number_format($stats['ventas_mes'], 0, ',', '.') }} este mes</p>
            </div>
            <div class="flex gap-1 bg-slate-50 p-1 rounded-xl border border-slate-100">
                @foreach(['hoy' => 'Hoy', 'semana' => 'Semana', 'mes' => 'Mes', 'año' => 'Año'] as $key => $label)
                <button @click="activePeriod = '{{ $key }}'"
                        :class="activePeriod === '{{ $key }}' ? 'bg-white text-slate-800 shadow-sm font-semibold' : 'text-slate-400 hover:text-slate-600'"
                        class="px-3 py-1.5 text-xs rounded-lg transition-all">{{ $label }}</button>
                @endforeach
            </div>
        </div>
        <div class="px-5 py-4">
            <svg viewBox="0 0 800 210" class="w-full" preserveAspectRatio="none" style="height:180px">
                <defs>
                    <linearGradient id="areaGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#1a56c4" stop-opacity="0.2"/>
                        <stop offset="100%" stop-color="#1a56c4" stop-opacity="0"/>
                    </linearGradient>
                </defs>
                {{-- Grid lines --}}
                @foreach([0, 0.25, 0.5, 0.75, 1] as $t)
                <line x1="10" x2="790" y1="{{ 10 + $t * 180 }}" y2="{{ 10 + $t * 180 }}"
                      stroke="#f1f5f9" stroke-width="1"/>
                @endforeach
                {{-- Area fill --}}
                <path :d="areaPath()" fill="url(#areaGrad)"/>
                {{-- Line --}}
                <path :d="svgPath()" fill="none" stroke="#1a56c4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                {{-- Last data point dot --}}
                <template x-if="currentData.length > 0">
                    <circle
                        :cx="10 + (currentData.length - 1) * (currentData.length > 1 ? 780/(currentData.length-1) : 780)"
                        :cy="10 + (1 - (currentData[currentData.length-1] - minVal) / (maxVal - minVal || 1)) * 180"
                        r="4" fill="#1a56c4" stroke="white" stroke-width="2"/>
                </template>
            </svg>
        </div>
    </div>

    {{-- Recent Orders (2/5 width) --}}
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
        <p class="text-xs text-slate-400 mb-5">Distribución este mes</p>
        {{-- Donut SVG --}}
        <div class="flex items-center justify-center mb-5">
            <svg viewBox="0 0 120 120" class="w-36 h-36">
                <circle cx="60" cy="60" r="48" fill="none" stroke="#e2e8f0" stroke-width="18"/>
                {{-- Webpay 65% = 301.5deg of 360 = 302/464.5 ≈ 301.6 --}}
                <circle cx="60" cy="60" r="48" fill="none" stroke="#1a56c4" stroke-width="18"
                    stroke-dasharray="195.75 105.66" stroke-dashoffset="0" transform="rotate(-90 60 60)"/>
                {{-- Transferencia 25% --}}
                <circle cx="60" cy="60" r="48" fill="none" stroke="#10b981" stroke-width="18"
                    stroke-dasharray="75.29 226.12" stroke-dashoffset="-195.75" transform="rotate(-90 60 60)"/>
                {{-- WhatsApp 10% --}}
                <circle cx="60" cy="60" r="48" fill="none" stroke="#f59e0b" stroke-width="18"
                    stroke-dasharray="30.12 271.29" stroke-dashoffset="-271.04" transform="rotate(-90 60 60)"/>
                <text x="60" y="56" text-anchor="middle" font-size="14" font-weight="700" fill="#1e293b" font-family="system-ui">65%</text>
                <text x="60" y="70" text-anchor="middle" font-size="7" fill="#94a3b8" font-family="system-ui">Webpay</text>
            </svg>
        </div>
        <div class="space-y-3">
            @foreach([['Webpay / Transbank', '65%', 'bg-[#1a56c4]'],['Transferencia','25%','bg-emerald-500'],['WhatsApp','10%','bg-amber-400']] as [$label,$pct,$dot])
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full {{ $dot }} shrink-0"></span>
                    <span class="text-sm text-slate-600">{{ $label }}</span>
                </div>
                <span class="text-sm font-bold text-slate-800">{{ $pct }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection
