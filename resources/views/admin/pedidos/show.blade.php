@extends('layouts.admin')
@section('title', 'Pedido #'.str_pad($order->id, 4, '0', STR_PAD_LEFT))
@section('page-title', 'Pedido #'.str_pad($order->id, 4, '0', STR_PAD_LEFT))
@section('breadcrumb', 'Pedidos / Detalle')

@section('content')

<div class="grid lg:grid-cols-3 gap-6">

    {{-- Main col --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Items --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="font-semibold text-slate-800">Productos del pedido</h2>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Producto</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Precio</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Cant.</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($order->items as $item)
                    <tr>
                        <td class="px-5 py-3">
                            <p class="font-medium text-slate-800">{{ $item->nombre_producto }}</p>
                            @if($item->product)
                            <p class="text-xs text-slate-400">SKU: {{ $item->product->sku }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right text-slate-600">${{ number_format($item->precio_unitario, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-slate-800">{{ $item->cantidad }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-slate-800">${{ number_format($item->precio_unitario * $item->cantidad, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t border-slate-200 bg-slate-50">
                    <tr>
                        <td colspan="3" class="px-5 py-2 text-right text-sm text-slate-500">Subtotal</td>
                        <td class="px-5 py-2 text-right text-sm font-semibold">${{ number_format($order->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($order->descuento > 0)
                    <tr>
                        <td colspan="3" class="px-5 py-2 text-right text-sm text-green-600">Descuento</td>
                        <td class="px-5 py-2 text-right text-sm font-semibold text-green-600">-${{ number_format($order->descuento, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="px-5 py-2 text-right text-sm text-slate-500">Despacho</td>
                        <td class="px-5 py-2 text-right text-sm font-semibold">
                            @if($order->costo_despacho == 0)
                            <span class="text-green-600">GRATIS</span>
                            @else
                            ${{ number_format($order->costo_despacho, 0, ',', '.') }}
                            @endif
                        </td>
                    </tr>
                    <tr class="border-t border-slate-200">
                        <td colspan="3" class="px-5 py-3 text-right font-bold text-slate-800">Total</td>
                        <td class="px-5 py-3 text-right font-bold text-lg text-[#0A3D7A]">${{ number_format($order->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Notas --}}
        @if($order->notas)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
            <h3 class="font-semibold text-amber-800 mb-2">Notas del cliente</h3>
            <p class="text-sm text-amber-700">{{ $order->notas }}</p>
        </div>
        @endif
    </div>

    {{-- Sidebar col --}}
    <div class="space-y-6">

        {{-- Cambiar estado --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="font-semibold text-slate-800 mb-4">Estado del pedido</h2>
            @php
                $badgeClass = ['pendiente'=>'bg-amber-100 text-amber-700','confirmado'=>'bg-blue-100 text-blue-700','enviado'=>'bg-purple-100 text-purple-700','entregado'=>'bg-green-100 text-green-700','cancelado'=>'bg-red-100 text-red-700'][$order->estado] ?? 'bg-slate-100 text-slate-600';
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $badgeClass }} mb-4">
                {{ $order->estadoLabel() }}
            </span>
            <form method="POST" action="{{ route('admin.pedidos.estado', $order) }}">
                @csrf @method('PATCH')
                <select name="estado" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm outline-none focus:border-[#1a56c4] mb-3">
                    @foreach(['pendiente'=>'Pendiente','confirmado'=>'Confirmado','enviado'=>'Enviado','entregado'=>'Entregado','cancelado'=>'Cancelado'] as $val => $label)
                    <option value="{{ $val }}" @selected($order->estado === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full py-2.5 bg-[#1a56c4] text-white text-sm font-semibold rounded-lg hover:bg-[#0A3D7A] transition-colors">
                    Actualizar estado
                </button>
            </form>
        </div>

        {{-- Info cliente --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="font-semibold text-slate-800 mb-4">Cliente</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-xs text-slate-400 uppercase font-semibold mb-0.5">Nombre</p>
                    <p class="text-slate-800 font-medium">{{ $order->nombre_cliente }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase font-semibold mb-0.5">Email</p>
                    <p class="text-slate-800">{{ $order->email_cliente }}</p>
                </div>
                @if($order->telefono)
                <div>
                    <p class="text-xs text-slate-400 uppercase font-semibold mb-0.5">Teléfono</p>
                    <p class="text-slate-800">{{ $order->telefono }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Dirección --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="font-semibold text-slate-800 mb-4">Dirección de entrega</h2>
            <div class="space-y-3 text-sm">
                <p class="text-slate-800">{{ $order->direccion }}</p>
                <p class="text-slate-800">{{ $order->comuna }}, {{ $order->ciudad }}</p>
                <div class="pt-2 border-t border-slate-100">
                    <p class="text-xs text-slate-400 uppercase font-semibold mb-0.5">Método de pago</p>
                    <p class="text-slate-800 font-medium">{{ $order->metodoPagoLabel() }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 uppercase font-semibold mb-0.5">Fecha del pedido</p>
                    <p class="text-slate-800">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <a href="{{ route('admin.pedidos.index') }}" class="block text-center py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-50 transition-colors">
            ← Volver a pedidos
        </a>
    </div>
</div>

@endsection
