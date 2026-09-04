@extends('layouts.app')
@section('title', 'Carrito de Compras')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-10"
     x-data="{
        get subtotal() { return $store.cart.subtotal; },
        get shipping() { return this.subtotal >= 15000 ? 0 : 2500; },
        get total() { return this.subtotal + this.shipping; },
     }">

    <div class="flex items-center gap-3 mb-8">
        <h1 class="text-3xl font-black section-title">Tu Carrito</h1>
        <span class="badge badge-blue" x-text="`${$store.cart.count} items`"></span>
    </div>

    <div x-show="$store.cart.items.length === 0" class="text-center py-24">
        <div class="w-20 h-20 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-[#1a56c4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-600 mb-3" style="font-family:'Poppins',sans-serif;">Tu carrito está vacío</h2>
        <p class="text-gray-400 mb-8">Agrega productos para continuar con tu compra</p>
        <a href="{{ route('productos.index') }}" class="btn-primary text-base px-10 py-4">Ver Productos</a>
    </div>

    <div x-show="$store.cart.items.length > 0" x-cloak>
        <div class="grid lg:grid-cols-3 gap-8 items-start">

            {{-- ── PRODUCTS TABLE ──────────────────────────────────────────── --}}
            <div class="lg:col-span-2">
                <div class="card overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 hidden md:grid grid-cols-12 gap-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <div class="col-span-6">Producto</div>
                        <div class="col-span-2 text-center">Precio</div>
                        <div class="col-span-2 text-center">Cantidad</div>
                        <div class="col-span-2 text-right">Subtotal</div>
                    </div>

                    <template x-for="item in $store.cart.items" :key="item.id">
                        <div class="px-6 py-4 border-b border-gray-50 last:border-0 grid grid-cols-12 gap-4 items-center">
                            {{-- Image + name --}}
                            <div class="col-span-12 md:col-span-6 flex items-center gap-4">
                                <div class="w-16 h-16 rounded-xl flex items-center justify-center shrink-0" style="background:linear-gradient(135deg,#EFF6FF,#DBEAFE);">
                                    <svg class="w-7 h-7 text-[#1a56c4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-800 text-sm leading-snug mb-0.5" x-text="item.name" style="font-family:'Poppins',sans-serif;"></div>
                                    <div class="text-xs text-gray-400" x-text="`SKU: PRD-${item.id}`"></div>
                                    <button @click="$store.cart.remove(item.id)"
                                        class="text-xs text-red-400 hover:text-red-600 mt-1 flex items-center gap-1 transition md:hidden">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Eliminar
                                    </button>
                                </div>
                            </div>

                            {{-- Unit price --}}
                            <div class="col-span-4 md:col-span-2 text-center">
                                <span class="text-sm font-semibold text-gray-700" x-text="`$${item.price.toLocaleString('es-CL')}`"></span>
                            </div>

                            {{-- Qty --}}
                            <div class="col-span-4 md:col-span-2 flex justify-center">
                                <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                                    <button @click="item.quantity = Math.max(1, item.quantity - 1)"
                                        class="px-2.5 py-1.5 text-gray-500 hover:bg-gray-50 text-sm font-bold transition">−</button>
                                    <span x-text="item.quantity" class="px-2.5 py-1.5 text-sm font-semibold min-w-[2rem] text-center"></span>
                                    <button @click="item.quantity += 1"
                                        class="px-2.5 py-1.5 text-gray-500 hover:bg-gray-50 text-sm font-bold transition">+</button>
                                </div>
                            </div>

                            {{-- Subtotal + delete --}}
                            <div class="col-span-4 md:col-span-2 flex items-center justify-end gap-2">
                                <span class="font-bold text-[#0A3D7A]" x-text="`$${(item.price * item.quantity).toLocaleString('es-CL')}`" style="font-family:'Poppins',sans-serif;"></span>
                                <button @click="$store.cart.remove(item.id)"
                                    class="hidden md:flex items-center justify-center w-7 h-7 rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>

                    {{-- Footer: clear + continue --}}
                    <div class="px-6 py-4 bg-gray-50 flex items-center justify-between">
                        <button @click="$store.cart.clear()" class="flex items-center gap-1.5 text-sm text-gray-400 hover:text-red-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Vaciar carrito
                        </button>
                        <a href="{{ route('productos.index') }}" class="flex items-center gap-1.5 text-sm font-semibold text-[#1a56c4] hover:text-[#0A3D7A] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Seguir comprando
                        </a>
                    </div>
                </div>
            </div>

            {{-- ── ORDER SUMMARY ────────────────────────────────────────────── --}}
            <div class="card p-6 sticky top-24">
                <h2 class="text-lg font-bold text-[#0A3D7A] mb-4" style="font-family:'Poppins',sans-serif;">Resumen del Pedido</h2>

                <div class="space-y-3 mb-4 pb-4 border-b border-gray-100">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Subtotal (<span x-text="$store.cart.count"></span> artículos)</span>
                        <span x-text="`$${subtotal.toLocaleString('es-CL')}`" class="font-medium"></span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Despacho estándar</span>
                        <span x-show="shipping === 0" class="text-[#1FA855] font-semibold">Gratis</span>
                        <span x-show="shipping > 0" x-text="`$${shipping.toLocaleString('es-CL')}`" class="font-medium"></span>
                    </div>
                    <div x-show="shipping > 0" class="text-xs text-gray-400 bg-blue-50 rounded-lg px-3 py-2">
                        Agrega <span x-text="`$${(15000-subtotal).toLocaleString('es-CL')}`" class="font-semibold text-[#1a56c4]"></span> más para despacho gratis
                    </div>
                </div>

                <div class="flex justify-between items-baseline mb-6">
                    <span class="text-base font-bold text-gray-800">Total</span>
                    <span x-text="`$${total.toLocaleString('es-CL')}`" class="text-2xl font-black text-[#0A3D7A]" style="font-family:'Poppins',sans-serif;"></span>
                </div>

                <a href="{{ route('checkout') }}" class="btn-primary w-full justify-center text-base py-3.5 mb-3">
                    IR A PAGAR
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                <a href="https://wa.me/56981493272?text=Hola!%20Quiero%20hacer%20un%20pedido" target="_blank"
                   class="btn-whatsapp w-full justify-center text-sm py-3">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Pedir por WhatsApp
                </a>

                {{-- Payment logos --}}
                <div class="mt-5 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-400 text-center mb-3">Medios de pago aceptados</p>
                    <div class="flex flex-wrap justify-center gap-2">
                        @foreach(['Webpay','Transferencia','MercadoPago','WhatsApp'] as $pm)
                        <span class="px-2.5 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-lg">{{ $pm }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
