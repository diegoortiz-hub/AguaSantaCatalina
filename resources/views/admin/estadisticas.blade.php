@extends('layouts.admin')
@section('title', 'Estadísticas')
@section('page-title', 'Estadísticas')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800" style="font-family:'Poppins',sans-serif;">Estadísticas y Reportes</h1>
        <p class="text-xs text-slate-500 mt-0.5">Análisis de rendimiento comercial, distribución de ventas y comportamiento de clientes</p>
    </div>
    <div class="flex bg-white border border-slate-200 rounded-xl p-1 shadow-sm gap-1">
        <span class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-[#1a56c4] text-white">Últimos 6 meses</span>
    </div>
</div>

{{-- ── KPIs ───────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <span class="text-xs text-slate-500 font-medium">Facturación del Mes</span>
        <p class="font-mono text-2xl font-extrabold text-slate-900 mt-1">${{ number_format($ventasMes, 0, ',', '.') }}</p>
        <div class="flex items-center gap-1 mt-2 text-xs font-bold {{ $varVentas >= 0 ? 'text-emerald-600' : 'text-rose-500' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $varVentas >= 0 ? 'M7 17l9.2-9.2M17 17V7H7' : 'M17 7l-9.2 9.2M7 7v10h10' }}"/></svg>
            {{ $varVentas >= 0 ? '+' : '' }}{{ $varVentas }}% vs mes anterior
        </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <span class="text-xs text-slate-500 font-medium">Ticket Promedio por Pedido</span>
        <p class="font-mono text-2xl font-extrabold text-slate-900 mt-1">${{ number_format($ticketProm, 0, ',', '.') }}</p>
        <p class="text-xs text-slate-400 mt-2">{{ $pedidosMes }} pedidos este mes</p>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <span class="text-xs text-slate-500 font-medium">Pedidos Confirmados</span>
        <p class="font-mono text-2xl font-extrabold text-slate-900 mt-1">{{ $pedidosMes }}</p>
        <p class="text-xs text-sky-600 font-bold mt-2">este mes</p>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <span class="text-xs text-slate-500 font-medium">Total de Clientes</span>
        <p class="font-mono text-2xl font-extrabold text-purple-700 mt-1">{{ \App\Models\User::where('rol','cliente')->count() }}</p>
        <p class="text-xs text-slate-400 mt-2">registrados en la tienda</p>
    </div>
</div>

{{-- ── Gráfico de barras mensual ──────────────────────────────────── --}}
<div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm mb-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-bold text-slate-900 text-base">Evolución Mensual de Ventas</h2>
            <p class="text-xs text-slate-400">Ingresos brutos por mes en pesos chilenos</p>
        </div>
    </div>
    <div class="grid gap-3 sm:gap-6 items-end border-b border-slate-100 pb-2" style="grid-template-columns:repeat({{ count($mensual) }},1fr); height:224px;">
        @foreach($mensual as $item)
        @php $pct = $maxMensual > 0 ? round(($item['ventas'] / $maxMensual) * 100) : 0; @endphp
        <div class="flex flex-col items-center gap-2 h-full justify-end group">
            <span class="text-[10px] font-mono text-slate-500 font-bold opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                ${{ number_format($item['ventas'], 0, ',', '.') }}
            </span>
            <div class="w-full rounded-t-xl bg-blue-100 group-hover:bg-[#1a56c4] transition-all flex flex-col justify-end overflow-hidden"
                 style="height:{{ $pct }}%">
                <div class="w-full bg-[#1a56c4] h-2 rounded-t-xl"></div>
            </div>
            <span class="text-xs font-semibold text-slate-700">{{ $item['mes'] }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- ── Dos columnas: Top Productos + Zonas/Pagos ─────────────────── --}}
<div class="grid lg:grid-cols-2 gap-6">

    {{-- Top Productos --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold text-slate-900 text-base flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                Productos Más Vendidos
            </h2>
            <span class="text-xs text-slate-400 font-mono">Ranking Top 4</span>
        </div>
        <div class="space-y-3">
            @forelse($topProductos as $idx => $prod)
            @php $sharePct = $maxRevenue > 0 ? round(($prod->total_revenue / $maxRevenue) * 90) : 0; @endphp
            <div class="flex flex-col gap-1.5 p-3 rounded-xl bg-slate-50 border border-slate-100">
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-slate-200 font-bold text-slate-700 flex items-center justify-center text-[10px]">{{ $idx + 1 }}</span>
                        <span class="font-bold text-slate-800 truncate max-w-[160px]">{{ $prod->nombre_producto }}</span>
                    </div>
                    <span class="font-mono font-bold text-slate-900">${{ number_format($prod->total_revenue, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-500 mt-0.5">
                    <span>{{ $prod->total_unidades }} unidades</span>
                    <span class="font-bold text-[#1a56c4]">{{ $sharePct }}% del total</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-[#1a56c4] h-full rounded-full" style="width:{{ $sharePct }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-sm text-slate-400 text-center py-6">Sin ventas registradas aún.</p>
            @endforelse
        </div>
    </div>

    {{-- Columna derecha: Estado de pedidos + Métodos de pago --}}
    <div class="flex flex-col gap-6">

        {{-- Pedidos por estado --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <h2 class="font-bold text-slate-900 text-base flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Distribución de Pedidos por Estado
            </h2>
            @php
                $estadoConfig = [
                    'pendiente'  => ['text-amber-700',  'bg-amber-100',  'Pendiente'],
                    'confirmado' => ['text-blue-700',   'bg-blue-100',   'Confirmado'],
                    'enviado'    => ['text-purple-700', 'bg-purple-100', 'Enviado'],
                    'entregado'  => ['text-emerald-700','bg-emerald-100','Entregado'],
                    'cancelado'  => ['text-rose-700',   'bg-rose-100',   'Cancelado'],
                ];
                $totalPedidos = $porEstado->sum() ?: 1;
            @endphp
            <div class="space-y-2.5">
                @foreach($estadoConfig as $key => [$tc, $bg, $label])
                @php $cnt = $porEstado[$key] ?? 0; $pct = round($cnt / $totalPedidos * 100); @endphp
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="font-semibold text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full {{ $bg }} border {{ str_replace('bg-', 'border-', $bg) }}"></span>
                            {{ $label }}
                        </span>
                        <span class="font-mono font-bold text-slate-800">{{ $cnt }} ({{ $pct }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="{{ $bg }} h-full rounded-full" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Métodos de pago --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <h2 class="font-bold text-slate-900 text-base flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-[#1a56c4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Recaudación por Pasarela
            </h2>
            @php
                $metodosConfig = [
                    'webpay'        => ['Webpay Plus',   'bg-blue-50 border-blue-100',   'text-blue-700'],
                    'transferencia' => ['Transferencia', 'bg-sky-50 border-sky-100',     'text-sky-700'],
                    'whatsapp'      => ['WhatsApp',      'bg-amber-50 border-amber-100', 'text-amber-700'],
                    'contra_entrega'=> ['Contra entrega','bg-slate-50 border-slate-100', 'text-slate-700'],
                ];
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-{{ min(count($porMetodo) + (count($porMetodo) < count($metodosConfig) ? 0 : 0), 4) }} gap-3">
                @foreach($metodosConfig as $key => [$label, $bg, $tc])
                @php $monto = $porMetodo[$key] ?? 0; $pct2 = round($monto / $totalPagos * 100); @endphp
                @if($monto > 0)
                <div class="p-3 {{ $bg }} border rounded-xl text-center">
                    <span class="text-[11px] font-bold {{ $tc }} block">{{ $label }}</span>
                    <p class="font-mono font-extrabold text-slate-900 text-sm mt-1">${{ number_format($monto, 0, ',', '.') }}</p>
                    <span class="text-[10px] text-slate-500 font-bold">{{ $pct2 }}% cuota</span>
                </div>
                @endif
                @endforeach
                @if($porMetodo->isEmpty())
                <div class="col-span-2 text-center text-sm text-slate-400 py-4">Sin ventas registradas.</div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
