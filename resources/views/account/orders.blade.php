@extends('layouts.app')
@section('title', 'Mis Pedidos')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-10">
    <div class="grid lg:grid-cols-4 gap-8 items-start">

        {{-- ── SIDEBAR ──────────────────────────────────────────────────────── --}}
        <aside class="lg:col-span-1">
            <div class="card overflow-hidden">
                {{-- Profile --}}
                <div class="p-5 text-center" style="background:linear-gradient(135deg,#0A3D7A,#1E6FBF);">
                    <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold text-white mx-auto mb-3">
                        {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}
                    </div>
                    <div class="font-semibold text-white text-sm" style="font-family:'Poppins',sans-serif;">{{ auth()->user()->nombre }}</div>
                    <div class="text-xs text-white/60 mt-0.5">{{ auth()->user()->email }}</div>
                </div>

                {{-- Nav links --}}
                @php
                $navLinks = [
                    ['account.orders', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'Mis Pedidos'],
                    ['mi-cuenta', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'Mi Perfil'],
                    ['home', 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'Volver a la tienda'],
                ];
                @endphp
                <nav class="divide-y divide-gray-50">
                    @foreach($navLinks as [$route, $svgPath, $label])
                    <a href="{{ route($route) }}"
                       class="flex items-center gap-3 px-5 py-3.5 text-sm transition hover:bg-gray-50
                              {{ request()->routeIs($route) ? 'bg-blue-50 text-[#0A3D7A] font-semibold border-r-4 border-[#1a56c4]' : 'text-gray-600' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $svgPath }}"/></svg>
                        {{ $label }}
                    </a>
                    @endforeach
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-5 py-3.5 text-sm text-red-500 hover:bg-red-50 transition text-left">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Cerrar sesión
                        </button>
                    </form>
                </nav>
            </div>
        </aside>

        {{-- ── ORDERS ───────────────────────────────────────────────────────── --}}
        <div class="lg:col-span-3">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-black section-title">Mis Pedidos</h1>
                <a href="{{ route('productos.index') }}" class="btn-primary text-sm py-2.5 px-5">
                    + Nuevo pedido
                </a>
            </div>

            @if($orders->isEmpty())
            <div class="card p-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-[#1a56c4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <h2 class="text-xl font-bold text-gray-600 mb-2" style="font-family:'Poppins',sans-serif;">Aún no tienes pedidos</h2>
                <p class="text-gray-400 mb-6">¡Explora nuestros productos y haz tu primer pedido!</p>
                <a href="{{ route('productos.index') }}" class="btn-primary px-8 py-3">Ver Productos</a>
            </div>
            @else
            <div class="space-y-4">
                @foreach($orders as $order)
                @php
                $badgeClasses = [
                    'pendiente'  => 'badge-orange',
                    'confirmado' => 'badge-blue',
                    'enviado'    => 'badge-purple',
                    'entregado'  => 'badge-green',
                    'cancelado'  => 'badge-red',
                ];
                @endphp
                <div class="card overflow-hidden">
                    {{-- Order header --}}
                    <div class="px-5 py-4 bg-gray-50 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="font-black text-[#0A3D7A] text-base" style="font-family:'Poppins',sans-serif;">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                            <span class="badge {{ $badgeClasses[$order->estado] ?? 'badge-gray' }}">{{ $order->estadoLabel() }}</span>
                        </div>
                        <div class="flex items-center gap-4 text-xs text-gray-400">
                            <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                            <span>·</span>
                            <span>{{ $order->metodoPagoLabel() }}</span>
                        </div>
                    </div>

                    {{-- Items --}}
                    <div class="px-5 py-3 space-y-2">
                        @foreach($order->items->take(3) as $item)
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-[#1a56c4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <span class="flex-1 text-gray-700">{{ $item->nombre_producto }}</span>
                            <span class="text-gray-400">× {{ $item->cantidad }}</span>
                            <span class="font-medium text-gray-700">${{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                        @if($order->items->count() > 3)
                        <p class="text-xs text-gray-400 pl-11">+ {{ $order->items->count() - 3 }} productos más</p>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                        <div class="text-sm">
                            <span class="text-gray-400">Total:</span>
                            <span class="font-black text-[#0A3D7A] text-base ml-1" style="font-family:'Poppins',sans-serif;">${{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('pedido.confirmacion', $order->id) }}"
                               class="btn-outline text-xs py-1.5 px-4">Ver detalle</a>
                            @if($order->estado === 'entregado')
                            <a href="{{ route('productos.index') }}"
                               class="btn-primary text-xs py-1.5 px-4">Volver a pedir</a>
                            @endif
                            @if($order->metodo_pago === 'whatsapp' && in_array($order->estado, ['pendiente','confirmado']))
                            <a href="https://wa.me/56981493272?text=Hola!%20Consulta%20sobre%20mi%20pedido%20%23{{ str_pad($order->id,5,'0',STR_PAD_LEFT) }}"
                               target="_blank" class="btn-whatsapp text-xs py-1.5 px-4">WhatsApp</a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($orders->hasPages())
            <div class="mt-6 flex justify-center">
                {{ $orders->links() }}
            </div>
            @endif
            @endif
        </div>
    </div>
</div>
@endsection
