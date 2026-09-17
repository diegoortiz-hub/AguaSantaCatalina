@extends('layouts.admin')
@section('title', $documento->numero())
@section('page-title', 'Boletas y facturas')

@section('content')

@php
    $pedido = $documento->order;
    $claseEstado = [
        'pendiente' => 'bg-amber-100 text-amber-700',
        'emitido'   => 'bg-emerald-100 text-emerald-700',
        'rechazado' => 'bg-rose-100 text-rose-700',
        'anulado'   => 'bg-slate-200 text-slate-600',
    ][$documento->estado] ?? 'bg-slate-100 text-slate-600';
@endphp

<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <a href="{{ route('admin.documentos.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-slate-700 transition-colors mb-2">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Volver al listado
        </a>
        <div class="flex flex-wrap items-center gap-3">
            <h1 class="text-2xl font-bold text-slate-800" style="font-family:'Poppins',sans-serif;">{{ $documento->numero() }}</h1>
            <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full {{ $claseEstado }}">{{ $documento->etiquetaEstado() }}</span>
        </div>
        <p class="text-sm text-slate-400 mt-1">
            {{ $documento->etiquetaTipo() }} · Pedido
            <a href="{{ route('admin.pedidos.show', $pedido) }}" class="text-[#1a56c4] hover:underline font-mono">#{{ str_pad($pedido->id, 4, '0', STR_PAD_LEFT) }}</a>
            · Registrado por «{{ $documento->emisor }}»
        </p>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        @if($documento->pdf_path)
        <a href="{{ route('admin.documentos.pdf', $documento) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Descargar PDF
        </a>
        @endif

        @if($documento->estaEmitido())
        <form method="POST" action="{{ route('admin.documentos.reenviar', $documento) }}">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#1a56c4] text-sm font-semibold text-white hover:bg-[#0A3D7A] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Enviar al cliente
            </button>
        </form>
        @endif
    </div>
</div>

@if($errors->any())
<div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-700">
    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
</div>
@endif

<div class="grid xl:grid-cols-5 gap-4">

    {{-- ── Registro del folio ──────────────────────────────────────────── --}}
    <div class="xl:col-span-3 space-y-4">

        @if($documento->estado !== 'anulado')
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <h2 class="font-bold text-slate-800 text-base">
                {{ $documento->estaEmitido() ? 'Datos del documento emitido' : 'Registrar la emisión' }}
            </h2>
            <p class="text-xs text-slate-400 mt-0.5 mb-5">
                {{ $documento->estaEmitido()
                    ? 'Puedes corregir el folio o la fecha si quedaron mal anotados.'
                    : 'Emite el documento donde corresponda y anota acá el folio que salió.' }}
            </p>

            <form method="POST" action="{{ route('admin.documentos.update', $documento) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <label for="tipo" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Tipo</label>
                        <select id="tipo" name="tipo" class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-[#1a56c4]">
                            @foreach(\App\Models\DocumentoTributario::TIPOS as $clave => $etiqueta)
                            <option value="{{ $clave }}" @selected(old('tipo', $documento->tipo) === $clave)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="folio" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Folio *</label>
                        <input id="folio" name="folio" type="number" min="1" required
                               value="{{ old('folio', $documento->folio) }}" placeholder="1042"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-[#1a56c4] tabular-nums">
                    </div>
                    <div>
                        <label for="fecha_emision" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Fecha *</label>
                        <input id="fecha_emision" name="fecha_emision" type="date" required max="{{ now()->toDateString() }}"
                               value="{{ old('fecha_emision', $documento->fecha_emision?->toDateString() ?? now()->toDateString()) }}"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-[#1a56c4]">
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="pdf" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">PDF del documento</label>
                        <input id="pdf" name="pdf" type="file" accept="application/pdf"
                               class="w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                        <p class="text-[11px] text-slate-400 mt-1">Hasta 4 MB. Es lo que se adjunta al correo del cliente.</p>
                    </div>
                    <div>
                        <label for="xml" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">XML (opcional)</label>
                        <input id="xml" name="xml" type="file"
                               class="w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                        <p class="text-[11px] text-slate-400 mt-1">Sólo para respaldo. No se envía al cliente.</p>
                    </div>
                </div>

                <div>
                    <label for="observaciones" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Observaciones</label>
                    <textarea id="observaciones" name="observaciones" rows="2"
                              class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-[#1a56c4]"
                              placeholder="Notas internas">{{ old('observaciones', $documento->observaciones) }}</textarea>
                </div>

                <label class="flex items-center gap-2.5 text-sm text-slate-600 select-none">
                    <input type="checkbox" name="avisar" value="1" checked
                           class="rounded border-slate-300 text-[#1a56c4] focus:ring-[#1a56c4]/30">
                    Enviar el documento por correo a {{ $pedido->email_cliente }}
                </label>

                <div class="flex items-center gap-2 pt-1">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#0A3D7A] text-sm font-semibold text-white hover:bg-[#1a56c4] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ $documento->estaEmitido() ? 'Guardar cambios' : 'Marcar como emitido' }}
                    </button>
                </div>
            </form>
        </div>
        @endif

        {{-- ── Detalle del pedido ──────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-50">
                <h2 class="font-bold text-slate-800 text-base">Detalle del pedido</h2>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left px-5 py-2.5 text-xs font-semibold text-slate-500 uppercase">Producto</th>
                        <th class="text-right px-5 py-2.5 text-xs font-semibold text-slate-500 uppercase">Cant.</th>
                        <th class="text-right px-5 py-2.5 text-xs font-semibold text-slate-500 uppercase">Precio</th>
                        <th class="text-right px-5 py-2.5 text-xs font-semibold text-slate-500 uppercase">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($pedido->items as $item)
                    <tr>
                        <td class="px-5 py-2.5 text-slate-700">{{ $item->nombre_producto }}</td>
                        <td class="px-5 py-2.5 text-right tabular-nums text-slate-600">{{ $item->cantidad }}</td>
                        <td class="px-5 py-2.5 text-right tabular-nums text-slate-600">${{ number_format($item->precio_unitario, 0, ',', '.') }}</td>
                        <td class="px-5 py-2.5 text-right tabular-nums font-semibold text-slate-800">${{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-5 py-4 border-t border-slate-100 space-y-1.5 text-sm">
                @foreach([
                    ['Subtotal', $pedido->subtotal, false],
                    ['Despacho', $pedido->costo_despacho, false],
                    ['Descuento', -$pedido->descuento, false],
                ] as [$rotulo, $valor, $fuerte])
                @if($rotulo !== 'Descuento' || $pedido->descuento > 0)
                <div class="flex justify-between text-slate-500">
                    <span>{{ $rotulo }}</span>
                    <span class="tabular-nums">${{ number_format($valor, 0, ',', '.') }}</span>
                </div>
                @endif
                @endforeach
                <div class="flex justify-between pt-2 border-t border-slate-100 text-slate-500">
                    <span>Neto</span>
                    <span class="tabular-nums">${{ number_format($documento->neto, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-500">
                    <span>IVA 19%</span>
                    <span class="tabular-nums">${{ number_format($documento->iva, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between pt-2 border-t border-slate-100 text-base font-bold text-slate-800">
                    <span>Total</span>
                    <span class="tabular-nums">${{ number_format($documento->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Receptor y anulación ───────────────────────────────────────── --}}
    <div class="xl:col-span-2 space-y-4">

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <h2 class="font-bold text-slate-800 text-base mb-4">Receptor</h2>

            @if($pedido->esFactura())
            <dl class="space-y-3 text-sm">
                @foreach([
                    ['Razón social', $pedido->razon_social],
                    ['RUT', $pedido->rut_receptor],
                    ['Giro', $pedido->giro],
                    ['Dirección', $pedido->direccion_factura],
                    ['Comuna', $pedido->comuna_factura],
                    ['Contacto', $pedido->nombre_cliente],
                    ['Email', $pedido->email_cliente],
                    ['Teléfono', $pedido->telefono],
                ] as [$rotulo, $valor])
                <div>
                    <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ $rotulo }}</dt>
                    <dd class="text-slate-700 mt-0.5 break-words">{{ $valor ?: '—' }}</dd>
                </div>
                @endforeach
            </dl>
            @else
            <p class="text-sm text-slate-500 leading-relaxed mb-4">
                Es una boleta: no identifica al comprador. Estos son los datos de contacto del pedido.
            </p>
            <dl class="space-y-3 text-sm">
                @foreach([
                    ['Nombre', $pedido->nombre_cliente],
                    ['Email', $pedido->email_cliente],
                    ['Teléfono', $pedido->telefono],
                    ['Dirección de entrega', trim(($pedido->direccion ?? '').' '.($pedido->comuna ?? ''))],
                ] as [$rotulo, $valor])
                <div>
                    <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ $rotulo }}</dt>
                    <dd class="text-slate-700 mt-0.5 break-words">{{ $valor ?: '—' }}</dd>
                </div>
                @endforeach
            </dl>
            @endif
        </div>

        @if($documento->observaciones)
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <h2 class="font-bold text-slate-800 text-base mb-2">Observaciones</h2>
            <p class="text-sm text-slate-600 whitespace-pre-line">{{ $documento->observaciones }}</p>
        </div>
        @endif

        @if($documento->estado === 'anulado')
        <div class="bg-slate-100 rounded-2xl border border-slate-200 p-5">
            <h2 class="font-bold text-slate-700 text-base">Documento anulado</h2>
            <p class="text-sm text-slate-500 mt-1">
                Anulado el {{ $documento->anulado_at?->format('d/m/Y H:i') }}. El registro se conserva: un documento
                anulado sigue siendo parte del histórico.
            </p>
        </div>
        @else
        <div class="bg-white rounded-2xl border border-rose-200 shadow-sm p-5"
             x-data="{ abierto: false }">
            <h2 class="font-bold text-slate-800 text-base">Anular documento</h2>
            <p class="text-xs text-slate-400 mt-0.5 mb-3">
                Marca el documento como anulado. No lo borra, y no anula nada ante el SII: eso se hace con una nota
                de crédito emitida donde corresponda.
            </p>

            <button x-show="!abierto" @click="abierto = true" type="button"
                    class="text-sm font-semibold text-rose-600 hover:underline">Anular este documento</button>

            <form x-show="abierto" x-cloak method="POST" action="{{ route('admin.documentos.anular', $documento) }}" class="space-y-3">
                @csrf
                <textarea name="motivo" rows="2" required minlength="5"
                          class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl outline-none focus:border-rose-400"
                          placeholder="Motivo de la anulación"></textarea>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 text-sm font-semibold text-white hover:bg-rose-700 transition-colors">Confirmar anulación</button>
                    <button type="button" @click="abierto = false" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">Cancelar</button>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>

@endsection
