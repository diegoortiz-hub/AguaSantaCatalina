{{--
    Cart Drawer — panel lateral derecho
    Activado vía $store.cart.open (Alpine.js)
    Incluir en layouts/app.blade.php antes de </body>
--}}
<div x-cloak>

    {{-- ── Backdrop ────────────────────────────────────────────────────────── --}}
    <div
        x-show="$store.cart.open"
        x-transition:enter="transition-opacity duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="$store.cart.open = false"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[998]"
    ></div>

    {{-- ── Drawer panel ─────────────────────────────────────────────────────── --}}
    <div
        x-show="$store.cart.open"
        x-transition:enter="transition-transform duration-300 ease-out"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition-transform duration-200 ease-in"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        @keydown.escape.window="$store.cart.open = false"
        class="fixed inset-y-0 right-0 z-[999] w-full max-w-md flex flex-col bg-white shadow-2xl"
        x-data="{
            commune: 'Las Condes',
            get communeShipping() {
                const premium = ['Colina / Chicureo'];
                const cost = premium.includes(this.commune) ? 3500 : 2500;
                return $store.cart.subtotal >= 15000 ? 0 : cost;
            }
        }"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-[#0A3D7A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h2 class="font-bold text-[#0A3D7A] text-base" style="font-family:'Poppins',sans-serif;">
                    Tu Carrito
                </h2>
                <span
                    x-show="$store.cart.count > 0"
                    x-text="`${$store.cart.count} ítem${$store.cart.count !== 1 ? 's' : ''}`"
                    class="text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700"
                ></span>
            </div>
            <button
                @click="$store.cart.open = false"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
                aria-label="Cerrar carrito"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- ── Free shipping progress bar ──────────────────────────────────── --}}
        <div x-show="$store.cart.items.length > 0" class="px-5 pt-3 pb-2">
            <div x-show="$store.cart.shippingProgress < 100">
                <p class="text-xs text-gray-500 mb-1.5">
                    Te faltan
                    <span class="font-bold text-[#1a56c4]" x-text="`$${$store.cart.missingForFreeShipping.toLocaleString('es-CL')}`"></span>
                    para despacho gratis
                </p>
            </div>
            <div x-show="$store.cart.shippingProgress >= 100">
                <p class="text-xs font-semibold text-green-700 mb-1.5 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    ¡Despacho gratis incluido!
                </p>
            </div>
            <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                <div
                    class="h-full rounded-full transition-all duration-500"
                    :style="`width:${$store.cart.shippingProgress}%; background:${$store.cart.shippingProgress >= 100 ? '#1FA855' : '#1a56c4'}`"
                ></div>
            </div>
        </div>

        {{-- ── Items list (scrollable) ─────────────────────────────────────── --}}
        <div class="flex-1 overflow-y-auto px-5 py-3 space-y-3">

            {{-- Empty state --}}
            <div x-show="$store.cart.items.length === 0" class="flex flex-col items-center justify-center h-full py-20 text-center">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-[#1a56c4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <p class="font-bold text-gray-500 text-base mb-1" style="font-family:'Poppins',sans-serif;">Tu carrito está vacío</p>
                <p class="text-sm text-gray-400 mb-6">Agrega productos para continuar</p>
                <button
                    @click="$store.cart.open = false"
                    class="btn-primary text-sm px-6 py-2.5"
                >
                    Ver Productos
                </button>
            </div>

            {{-- Item rows --}}
            <template x-for="item in $store.cart.items" :key="item.id">
                <div class="flex items-start gap-3 py-3 border-b border-gray-50 last:border-0">
                    {{-- Thumbnail --}}
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center shrink-0" style="background:linear-gradient(135deg,#EFF6FF,#DBEAFE);">
                        <svg class="w-6 h-6 text-[#1a56c4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-gray-800 text-sm leading-snug truncate" x-text="item.name" style="font-family:'Poppins',sans-serif;"></div>
                        <div class="text-xs text-gray-400 mt-0.5" x-text="`$${item.price.toLocaleString('es-CL')} c/u`"></div>
                        {{-- Qty controls --}}
                        <div class="flex items-center gap-2 mt-2">
                            <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                                <button
                                    @click="$store.cart.updateQty(item.id, -1)"
                                    class="px-2 py-1 text-gray-500 hover:bg-gray-50 text-sm font-bold transition leading-none"
                                >−</button>
                                <span x-text="item.quantity" class="px-2 py-1 text-sm font-semibold min-w-[2rem] text-center leading-none"></span>
                                <button
                                    @click="$store.cart.updateQty(item.id, 1)"
                                    class="px-2 py-1 text-gray-500 hover:bg-gray-50 text-sm font-bold transition leading-none"
                                >+</button>
                            </div>
                            <button
                                @click="$store.cart.remove(item.id)"
                                class="text-xs text-gray-300 hover:text-red-500 transition ml-1"
                                aria-label="Eliminar"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    {{-- Subtotal --}}
                    <div class="text-sm font-bold text-[#0A3D7A] shrink-0 pt-0.5" x-text="`$${(item.price * item.quantity).toLocaleString('es-CL')}`" style="font-family:'Poppins',sans-serif;"></div>
                </div>
            </template>
        </div>

        {{-- ── Bottom panel (always visible) ──────────────────────────────── --}}
        <div x-show="$store.cart.items.length > 0" class="border-t border-gray-100 px-5 pt-4 pb-5 space-y-4 bg-white">

            {{-- Commune selector --}}
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <select
                    x-model="commune"
                    class="flex-1 text-xs border border-gray-200 rounded-lg px-2 py-1.5 text-gray-700 bg-white outline-none focus:border-blue-400 cursor-pointer"
                >
                    @foreach([
                        'Las Condes','Providencia','Vitacura','Ñuñoa','Santiago Centro',
                        'La Reina','Lo Barnechea','La Florida','Macul','San Miguel',
                        'Peñalolén','Maipú','Huechuraba','Quilicura','Puente Alto','Colina / Chicureo'
                    ] as $c)
                    <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
                <span
                    class="text-xs font-semibold px-2 py-1 rounded-lg"
                    :class="communeShipping === 0 ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600'"
                    x-text="communeShipping === 0 ? 'Gratis' : `$${communeShipping.toLocaleString('es-CL')}`"
                ></span>
            </div>

            {{-- Coupon --}}
            <div x-show="!$store.cart.couponSuccess">
                <form @submit.prevent="$store.cart.applyCoupon()" class="flex gap-2">
                    <input
                        x-model="$store.cart.couponCode"
                        type="text"
                        placeholder="Código de descuento"
                        class="flex-1 text-xs border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-blue-400 uppercase tracking-wide"
                    >
                    <button type="submit" class="text-xs font-bold px-3 py-2 rounded-lg border-2 border-[#1a56c4] text-[#1a56c4] hover:bg-[#1a56c4] hover:text-white transition shrink-0">
                        Aplicar
                    </button>
                </form>
                <p
                    x-show="$store.cart.couponMessage && !$store.cart.couponSuccess"
                    x-text="$store.cart.couponMessage"
                    class="text-xs text-red-500 mt-1"
                ></p>
            </div>
            <div x-show="$store.cart.couponSuccess" class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg px-3 py-2">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-xs font-semibold text-green-800" x-text="$store.cart.couponMessage"></span>
                </div>
                <button @click="$store.cart.removeCoupon()" class="text-xs text-green-600 hover:text-green-800 font-semibold">✕</button>
            </div>

            {{-- Totals --}}
            <div class="space-y-1.5">
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Subtotal</span>
                    <span x-text="`$${$store.cart.subtotal.toLocaleString('es-CL')}`"></span>
                </div>
                <div x-show="$store.cart.couponDiscount > 0" class="flex justify-between text-sm text-green-700">
                    <span>Descuento cupón</span>
                    <span x-text="`-$${$store.cart.discountAmount.toLocaleString('es-CL')}`"></span>
                </div>
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Despacho</span>
                    <span
                        :class="communeShipping === 0 ? 'text-green-700 font-semibold' : ''"
                        x-text="communeShipping === 0 ? '¡Gratis!' : `$${communeShipping.toLocaleString('es-CL')}`"
                    ></span>
                </div>
                <div class="flex justify-between items-baseline pt-2 border-t border-gray-100">
                    <span class="font-bold text-gray-800">Total</span>
                    <span
                        class="text-xl font-black text-[#0A3D7A]"
                        style="font-family:'Poppins',sans-serif;"
                        x-text="`$${($store.cart.subtotal - $store.cart.discountAmount + communeShipping).toLocaleString('es-CL')}`"
                    ></span>
                </div>
            </div>

            {{-- CTAs --}}
            <div class="space-y-2 pt-1">
                <a
                    href="{{ route('checkout') }}"
                    class="btn-primary w-full justify-center py-3.5 text-sm"
                    @click="$store.cart.open = false"
                >
                    IR A PAGAR
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <button
                    @click="$store.cart.orderViaWhatsApp()"
                    class="btn-whatsapp w-full justify-center py-3 text-sm"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Pedir por WhatsApp
                </button>
                <button
                    @click="$store.cart.clear()"
                    class="w-full text-center text-xs text-gray-400 hover:text-red-500 transition py-1"
                >
                    Vaciar carrito
                </button>
            </div>
        </div>
    </div>
</div>
