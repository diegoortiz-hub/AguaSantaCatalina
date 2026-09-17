@extends('layouts.admin')
@section('title', 'Boletas y facturas')
@section('page-title', 'Boletas y facturas')

@section('content')

@php
    $tabs = ['todos' => 'Todos', 'pendiente' => 'Pendientes', 'emitido' => 'Emitidos', 'anulado' => 'Anulados'];
    $estadoActual = request('estado', '');

    $claseEstado = [
        'pendiente' => 'bg-amber-100 text-amber-700',
        'emitido'   => 'bg-emerald-100 text-emerald-700',
        'rechazado' => 'bg-rose-100 text-rose-700',
        'anulado'   => 'bg-slate-200 text-slate-600',
    ];
@endphp

{{-- ── Resumen del mes ──────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-6">
    <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
        <div>
            <h2 class="font-bold text-slate-800 text-base">Resumen del período</h2>
            <p class="text-xs text-slate-400 mt-0.5">Sólo documentos emitidos. Lo pendiente y lo anulado no se declara.</p>
        </div>

        <form method="GET" class="flex flex-wrap items-center gap-2">
            @if($estadoActual)<input type="hidden" name="estado" value="{{ $estadoActual }}">@endif
            <input type="month" name="mes" value="{{ $resumenMes['mes'] }}"
                   class="px-3 py-2 text-sm border border-slate-200 rounded-xl outline-none focus:border-[#1a56c4]">
            <button type="submit" class="px-4 py-2 text-sm font-semibold bg-slate-800 text-white rounded-xl hover:bg-slate-700 transition-colors">Ver</button>
            <a href="{{ route('admin.documentos.libro', ['mes' => $resumenMes['mes']]) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Libro de ventas
            </a>
        </form>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-px bg-slate-100 rounded-xl overflow-hidden">
        @foreach([
            ['Documentos', $resumenMes['cantidad'].'', $resumenMes['boletas'].' boletas · '.$resumenMes['facturas'].' facturas'],
            ['Neto',   '$'.number_format($resumenMes['neto'], 0, ',', '.'),  'Base imponible'],
            ['IVA 19%', '$'.number_format($resumenMes['iva'], 0, ',', '.'),  'Débito fiscal'],
            ['Total',  '$'.number_format($resumenMes['total'], 0, ',', '.'), 'Neto + IVA'],
            ['Pendientes', $conteos['pendiente'].'', 'Sin folio registrado'],
        ] as [$rotulo, $valor, $nota])
        <div class="bg-white px-4 py-3.5">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ $rotulo }}</p>
            <p class="text-lg font-bold text-slate-800 mt-1 tabular-nums" style="font-family:'Poppins',sans-serif;">{{ $valor }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">{{ $nota }}</p>
        </div>
        @endforeach
    </div>
</div>

{{-- ── Pedidos sin documento ───────────────────────────────────────────── --}}
@if($sinDocumento->isNotEmpty())
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-6">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 shrink-0 mt-0.5 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5 19h14a2 2 0 001.84-2.75L13.74 4a2 2 0 00-3.5 0L3.16 16.25A2 2 0 005 19z"/></svg>
        <div class="min-w-0 flex-1">
            <h3 class="font-bold text-amber-900 text-sm">{{ $sinDocumento->count() }} {{ \Illuminate\Support\Str::plural('pedido', $sinDocumento->count()) }} sin documento registrado</h3>
            <p class="text-xs text-amber-700 mt-0.5 mb-3">Son pedidos donde el registro no se creó. Regístralo para que entren al libro de ventas.</p>

            <div class="space-y-2">
                @foreach($sinDocumento as $pedido)
                <div class="flex flex-wrap items-center gap-3 bg-white rounded-xl border border-amber-200 px-3.5 py-2.5">
                    <span class="font-mono text-xs text-slate-500">#{{ str_pad($pedido->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <span class="text-sm font-semibold text-slate-800 truncate">{{ $pedido->razon_social ?: $pedido->nombre_cliente }}</span>
                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">{{ $pedido->documentoLabel() }}</span>
                    <span class="text-sm font-bold text-slate-700 tabular-nums ml-auto">${{ number_format($pedido->total, 0, ',', '.') }}</span>
                    <form method="POST" action="{{ route('admin.documentos.store', $pedido) }}">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-[#0A3D7A] text-white hover:bg-[#1a56c4] transition-colors">
                            Registrar
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

{{-- ── Filtros ─────────────────────────────────────────────────────────── --}}
<div class="flex items-center gap-1 mb-4 flex-wrap">
    @foreach($tabs as $clave => $etiqueta)
    @php
        $n = $clave === 'todos' ? $conteos['todos'] : ($conteos[$clave] ?? 0);
        $activo = ($clave === 'todos' && ! $estadoActual) || $estadoActual === $clave;
    @endphp
    <a href="{{ route('admin.documentos.index', $clave !== 'todos' ? ['estado' => $clave] : []) }}"
       class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium transition-colors
              {{ $activo ? 'bg-[#0A3D7A] text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
        {{ $etiqueta }}
        <span class="text-xs font-bold {{ $activo ? 'bg-white/20' : 'bg-slate-100' }} px-1.5 py-0.5 rounded-full">{{ $n }}</span>
    </a>
    @endforeach
</div>

<form method="GET" class="flex flex-wrap gap-2 mb-6">
    @if($estadoActual)<input type="hidden" name="estado" value="{{ $estadoActual }}">@endif
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Folio, cliente, RUT, razón social o N° de pedido…"
           class="flex-1 min-w-[240px] max-w-md px-3 py-2 text-sm border border-slate-200 rounded-xl outline-none focus:border-[#1a56c4]">
    <select name="tipo" class="px-3 py-2 text-sm border border-slate-200 rounded-xl outline-none focus:border-[#1a56c4]">
        <option value="">Todo tipo</option>
        @foreach(\App\Models\DocumentoTributario::TIPOS as $clave => $etiqueta)
        <option value="{{ $clave }}" @selected(request('tipo') === $clave)>{{ $etiqueta }}</option>
        @endforeach
    </select>
    <button type="submit" class="px-4 py-2 text-sm font-semibold bg-slate-800 text-white rounded-xl hover:bg-slate-700 transition-colors">Buscar</button>
</form>

{{-- ── Tabla ───────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Documento</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Receptor</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase hidden lg:table-cell">Pedido</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase hidden md:table-cell">Neto</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase hidden md:table-cell">IVA</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Total</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Estado</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($documentos as $doc)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-3">
                        <p class="font-semibold text-slate-800">{{ $doc->numero() }}</p>
                        <p class="text-xs text-slate-400">{{ $doc->fecha_emision?->format('d/m/Y') ?? 'Sin emitir' }}</p>
                    </td>
                    <td class="px-5 py-3">
                        <p class="font-medium text-slate-700 truncate max-w-[200px]">{{ $doc->order->razon_social ?: $doc->order->nombre_cliente }}</p>
                        <p class="text-xs text-slate-400">{{ $doc->order->rut_receptor ?: $doc->order->email_cliente }}</p>
                    </td>
                    <td class="px-5 py-3 hidden lg:table-cell">
                        <a href="{{ route('admin.pedidos.show', $doc->order) }}" class="font-mono text-xs text-[#1a56c4] hover:underline">
                            #{{ str_pad($doc->order_id, 4, '0', STR_PAD_LEFT) }}
                        </a>
                    </td>
                    <td class="px-5 py-3 text-right tabular-nums text-slate-600 hidden md:table-cell">${{ number_format($doc->neto, 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-right tabular-nums text-slate-600 hidden md:table-cell">${{ number_format($doc->iva, 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-right tabular-nums font-bold text-slate-800">${{ number_format($doc->total, 0, ',', '.') }}</td>
                    <td class="px-5 py-3">
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $claseEstado[$doc->estado] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ $doc->etiquetaEstado() }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('admin.documentos.show', $doc) }}" class="text-xs font-semibold text-[#1a56c4] hover:underline whitespace-nowrap">
                            {{ $doc->estado === 'pendiente' ? 'Registrar folio' : 'Ver' }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center">
                        <p class="text-sm font-semibold text-slate-500">No hay documentos con ese filtro</p>
                        <p class="text-xs text-slate-400 mt-1">Cada pedido nuevo registra el suyo automáticamente.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($documentos->hasPages())
    <div class="px-5 py-3 border-t border-slate-100">{{ $documentos->links() }}</div>
    @endif
</div>

{{-- Aviso permanente: el sistema no emite, registra. --}}
<div class="mt-6 flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-5">
    <svg class="w-5 h-5 shrink-0 mt-0.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <div class="text-sm text-slate-600 leading-relaxed">
        <p class="font-semibold text-slate-800">Este módulo registra documentos, no los emite.</p>
        <p class="mt-1">
            La boleta o factura se emite donde corresponda —el portal del SII o el sistema del contador— y acá se
            registra el folio y se adjunta el PDF, que queda disponible para reenviar al cliente y para el libro de ventas.
            Cuando se integre un proveedor autorizado, el folio va a llegar solo y esta pantalla no cambia.
        </p>
    </div>
</div>

@endsection
