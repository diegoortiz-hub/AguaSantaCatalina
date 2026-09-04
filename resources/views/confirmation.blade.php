@extends('layouts.app')
@section('title', '¡Pedido Confirmado!')

@section('content')

<div class="max-w-3xl mx-auto px-4 py-16 text-center">

    {{-- Animated check --}}
    <div class="flex justify-center mb-8">
        <div class="relative w-28 h-28">
            <svg class="w-28 h-28" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle class="circle-animate" cx="50" cy="50" r="45" stroke="#1FA855" stroke-width="6"
                    fill="none" stroke-dasharray="282" stroke-dashoffset="282"/>
                <path class="check-animate" d="M28 50 L43 65 L72 35" stroke="#1FA855" stroke-width="7"
                    stroke-linecap="round" stroke-linejoin="round" fill="none"
                    stroke-dasharray="60" stroke-dashoffset="60"/>
            </svg>
        </div>
    </div>

    <h1 class="text-4xl font-black text-[#0A3D7A] mb-3" style="font-family:'Poppins',sans-serif;">¡Gracias por tu compra!</h1>
    <p class="text-gray-500 text-lg mb-2">Tu pedido ha sido recibido correctamente.</p>
    <p class="text-gray-400 mb-8">Te enviaremos una confirmación a <strong class="text-gray-600">{{ $order->email_cliente }}</strong></p>

    {{-- Order number badge --}}
    <div class="inline-flex items-center gap-3 bg-blue-50 border border-blue-100 rounded-2xl px-6 py-3 mb-10">
        <div class="w-10 h-10 rounded-xl bg-[#0A3D7A]/10 flex items-center justify-center">
            <svg class="w-5 h-5 text-[#0A3D7A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div class="text-left">
            <div class="text-xs text-gray-400 uppercase tracking-wide">Número de pedido</div>
            <div class="text-xl font-black text-[#0A3D7A]" style="font-family:'Poppins',sans-serif;">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>

    {{-- Order detail card --}}
    <div class="card text-left mb-8 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-[#0A3D7A]" style="font-family:'Poppins',sans-serif;">Detalle del pedido</h2>
            <span class="badge badge-orange">{{ $order->estadoLabel() }}</span>
        </div>

        <div class="divide-y divide-gray-50">
            @foreach($order->items as $item)
            <div class="px-6 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-[#1a56c4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-800">{{ $item->nombre_producto }}</div>
                        <div class="text-xs text-gray-400">× {{ $item->cantidad }} · ${{ number_format($item->precio_unitario, 0, ',', '.') }} c/u</div>
                    </div>
                </div>
                <span class="font-semibold text-[#0A3D7A]">${{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 space-y-1.5 text-sm">
            <div class="flex justify-between text-gray-500">
                <span>Subtotal</span>
                <span>${{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-gray-500">
                <span>Despacho</span>
                <span>{{ $order->costo_despacho == 0 ? 'Gratis ✓' : '$'.number_format($order->costo_despacho, 0, ',', '.') }}</span>
            </div>
            @if($order->descuento > 0)
            <div class="flex justify-between text-green-600">
                <span>Descuento</span>
                <span>-${{ number_format($order->descuento, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between pt-2 border-t border-gray-200 font-black text-base text-[#0A3D7A]" style="font-family:'Poppins',sans-serif;">
                <span>Total</span>
                <span>${{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Shipping & payment info --}}
        <div class="px-6 py-4 grid sm:grid-cols-2 gap-4 text-sm border-t border-gray-100">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">Método de pago</p>
                <p class="font-medium text-gray-700">{{ $order->metodoPagoLabel() }}</p>
            </div>
            @if($order->direccion)
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-1">Dirección de entrega</p>
                <p class="font-medium text-gray-700">{{ $order->direccion }}, {{ $order->comuna }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Transferencia instructions --}}
    @if($order->metodo_pago === 'transferencia')
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-8 text-left">
        <h3 class="font-bold text-amber-900 mb-2 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
            Instrucciones de pago
        </h3>
        <div class="text-sm text-amber-800 space-y-1">
            <p><strong>Banco BCI</strong> · Cuenta Corriente Nº 12345678</p>
            <p><strong>Titular:</strong> Aguas Santa Catalina SpA</p>
            <p><strong>RUT:</strong> 76.543.210-1</p>
            <p><strong>Monto a transferir:</strong> ${{ number_format($order->total, 0, ',', '.') }}</p>
            <p class="mt-2 text-amber-700 flex items-center gap-1.5"><svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> Envía el comprobante con tu Nº de pedido a: <strong>pedidos@aguassantacatalina.cl</strong></p>
        </div>
    </div>
    @endif

    {{-- CTAs --}}
    <div class="flex flex-wrap justify-center gap-3">
        <a href="{{ route('home') }}" class="btn-outline text-base px-8 py-3.5">
            ← Volver al inicio
        </a>
        @if($order->metodo_pago === 'whatsapp')
        <a href="https://wa.me/56981493272?text=Hola!%20Quiero%20confirmar%20mi%20pedido%20%23{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}"
           target="_blank" class="btn-whatsapp text-base px-8 py-3.5">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Confirmar por WhatsApp
        </a>
        @endif
        <a href="{{ route('account.orders') }}" class="btn-primary text-base px-8 py-3.5">
            IR A MIS PEDIDOS →
        </a>
    </div>
</div>
@endsection
