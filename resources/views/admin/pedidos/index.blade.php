@extends('layouts.admin')
@section('title', 'Pedidos')
@section('page-title', 'Pedidos')
@section('breadcrumb', 'Gestión de pedidos')

@section('content')

{{-- Status tabs --}}
@php
    $tabs = ['todos'=>'Todos','pendiente'=>'Pendientes','confirmado'=>'Confirmados','enviado'=>'Enviados','entregado'=>'Entregados','cancelado'=>'Cancelados'];
    $currentEstado = request('estado', '');
    $badgeColors = ['pendiente'=>'bg-amber-500','confirmado'=>'bg-blue-500','enviado'=>'bg-purple-500','entregado'=>'bg-green-500','cancelado'=>'bg-red-500'];
@endphp

<div class="flex items-center gap-1 mb-6 flex-wrap">
    @foreach($tabs as $key => $label)
    @php $count = $key === 'todos' ? $conteos['todos'] : ($conteos[$key] ?? 0); $isActive = ($key === 'todos' && !$currentEstado) || $currentEstado === $key; @endphp
    <a href="{{ route('admin.pedidos.index', $key !== 'todos' ? ['estado'=>$key] : []) }}"
       class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium transition-colors
              {{ $isActive ? 'bg-[#0A3D7A] text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
        {{ $label }}
        <span class="text-xs font-bold {{ $isActive ? 'bg-white/20' : 'bg-slate-100' }} px-1.5 py-0.5 rounded-full">{{ $count }}</span>
    </a>
    @endforeach
</div>

{{-- Search --}}
<form method="GET" class="flex gap-2 mb-6">
    @if($currentEstado)<input type="hidden" name="estado" value="{{ $currentEstado }}">@endif
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre, email o #ID..."
           class="flex-1 max-w-sm px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-[#1a56c4]">
    <button type="submit" class="px-4 py-2 text-sm font-semibold bg-slate-800 text-white rounded-lg hover:bg-slate-700">Buscar</button>
</form>

{{-- Table --}}
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">#</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Cliente</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase hidden md:table-cell">Comuna</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Total</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Estado</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase hidden lg:table-cell">Pago</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase hidden lg:table-cell">Fecha</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($pedidos as $pedido)
                @php
                    $badgeClass = ['pendiente'=>'bg-amber-100 text-amber-700','confirmado'=>'bg-blue-100 text-blue-700','enviado'=>'bg-purple-100 text-purple-700','entregado'=>'bg-green-100 text-green-700','cancelado'=>'bg-red-100 text-red-700'][$pedido->estado] ?? 'bg-slate-100 text-slate-600';
                @endphp
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-3 font-mono text-xs text-slate-500">#{{ str_pad($pedido->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-5 py-3">
                        <p class="font-semibold text-slate-800">{{ $pedido->nombre_cliente }}</p>
                        <p class="text-xs text-slate-400">{{ $pedido->email_cliente }}</p>
                    </td>
                    <td class="px-5 py-3 text-slate-600 hidden md:table-cell">{{ $pedido->comuna }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-slate-800">${{ number_format($pedido->total, 0, ',', '.') }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                            {{ $pedido->estadoLabel() }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-xs text-slate-500 hidden lg:table-cell">{{ $pedido->metodoPagoLabel() }}</td>
                    <td class="px-5 py-3 text-xs text-slate-400 hidden lg:table-cell">{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-3">
                        <a href="{{ route('admin.pedidos.show', $pedido) }}"
                           class="px-3 py-1.5 text-xs font-semibold border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                            Ver
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-sm text-slate-400">No hay pedidos.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pedidos->hasPages())
    <div class="px-5 py-4 border-t border-slate-100">{{ $pedidos->links() }}</div>
    @endif
</div>

@endsection
