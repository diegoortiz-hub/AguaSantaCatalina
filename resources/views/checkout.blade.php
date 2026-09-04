@extends('layouts.app')
@section('title', 'Checkout')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-10"
     x-data="{
        step: 1,
        shipping: 'standard',
        payment: 'webpay',
        coupon: '',
        couponMsg: '',
        couponDiscount: 0,
        couponValid: false,
        nombre: '', email: '', telefono: '', direccion: '', comuna: '', ciudad: '',
        touched: false,
        get subtotal() { return $store.cart.subtotal; },
        get shippingCost() { return this.subtotal >= 15000 ? 0 : (this.shipping === 'express' ? 3990 : 2500); },
        get total() { return this.subtotal + this.shippingCost - this.couponDiscount; },
        goStep2() {
            this.touched = true;
            if (!this.nombre.trim() || !this.email.trim() || !this.direccion.trim() || !this.comuna.trim()) return;
            this.step = 2;
            this.$nextTick(() => window.scrollTo({top:0,behavior:'smooth'}));
        },
        async validateCoupon() {
            if (!this.coupon.trim()) return;
            const res = await fetch('/api/coupons/validate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]').content, Accept: 'application/json' },
                body: JSON.stringify({ codigo: this.coupon, subtotal: this.subtotal })
            });
            const data = await res.json();
            this.couponMsg = data.message;
            this.couponValid = data.valido;
            this.couponDiscount = data.valido ? data.descuento : 0;
        },
        async submitOrder() {
            const body = {
                session_id: $store.cart.sessionId,
                nombre_cliente: this.nombre,
                email_cliente: this.email,
                telefono: this.telefono,
                direccion: this.direccion,
                comuna: this.comuna,
                ciudad: this.ciudad,
                metodo_pago: this.payment,
                cupon: this.coupon,
            };
            const res = await fetch('/api/orders', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]').content, Accept: 'application/json' },
                body: JSON.stringify(body),
            });
            const order = await res.json();
            if (res.ok) {
                $store.cart.clear();
                window.location = `/pedido/${order.id}/confirmacion`;
            } else {
                alert(order.message || 'Error al procesar el pedido');
            }
        },
     }">

    {{-- Step indicator --}}
    <div class="flex items-center justify-center gap-0 mb-10 max-w-lg mx-auto">
        @foreach([[1,'Dirección'],[2,'Despacho'],[3,'Pago']] as [$n,$label])
        <div class="flex items-center {{ !$loop->first ? 'flex-1' : '' }}">
            @if(!$loop->first)
            <div class="flex-1 h-0.5 mx-2" :class="step >= {{ $n }} ? 'bg-[#1a56c4]' : 'bg-gray-200'"></div>
            @endif
            <div class="flex flex-col items-center">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition-all"
                     :class="step > {{ $n }} ? 'step-done' : (step === {{ $n }} ? 'step-active' : 'step-inactive')">
                    <span x-show="step <= {{ $n }}">{{ $n }}</span>
                    <span x-show="step > {{ $n }}">✓</span>
                </div>
                <span class="text-xs mt-1 font-medium" :class="step >= {{ $n }} ? 'text-[#0A3D7A]' : 'text-gray-400'">{{ $label }}</span>
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-3 gap-8 items-start">

        {{-- ── FORM ─────────────────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Step 1: Dirección --}}
            <div x-show="step === 1" x-transition>
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-[#0A3D7A] mb-5 flex items-center gap-2" style="font-family:'Poppins',sans-serif;">
                        <span class="w-7 h-7 step-active rounded-full flex items-center justify-center text-sm">1</span>
                        Datos de contacto y dirección
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold mb-1.5 uppercase tracking-wide"
                                   :class="touched && !nombre.trim() ? 'text-red-500' : 'text-gray-600'">Nombre completo *</label>
                            <input x-model="nombre" type="text" placeholder="María González"
                                   :class="touched && !nombre.trim() ? 'form-input border-red-400 focus:border-red-400 focus:ring-red-400/20' : 'form-input'">
                            <p x-show="touched && !nombre.trim()" class="text-xs text-red-500 mt-1">Este campo es obligatorio</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1.5 uppercase tracking-wide"
                                   :class="touched && !email.trim() ? 'text-red-500' : 'text-gray-600'">Email *</label>
                            <input x-model="email" type="email" placeholder="maria@ejemplo.cl"
                                   :class="touched && !email.trim() ? 'form-input border-red-400 focus:border-red-400 focus:ring-red-400/20' : 'form-input'">
                            <p x-show="touched && !email.trim()" class="text-xs text-red-500 mt-1">Ingresa un email válido</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Teléfono</label>
                            <input x-model="telefono" type="tel" placeholder="+56 9 1234 5678" class="form-input">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold mb-1.5 uppercase tracking-wide"
                                   :class="touched && !direccion.trim() ? 'text-red-500' : 'text-gray-600'">Dirección *</label>
                            <input x-model="direccion" type="text" placeholder="Av. Providencia 1234, Dpto 5"
                                   :class="touched && !direccion.trim() ? 'form-input border-red-400 focus:border-red-400 focus:ring-red-400/20' : 'form-input'">
                            <p x-show="touched && !direccion.trim()" class="text-xs text-red-500 mt-1">Ingresa tu dirección de entrega</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1.5 uppercase tracking-wide"
                                   :class="touched && !comuna.trim() ? 'text-red-500' : 'text-gray-600'">Comuna *</label>
                            <input x-model="comuna" type="text" placeholder="Providencia"
                                   :class="touched && !comuna.trim() ? 'form-input border-red-400 focus:border-red-400 focus:ring-red-400/20' : 'form-input'">
                            <p x-show="touched && !comuna.trim()" class="text-xs text-red-500 mt-1">Ingresa tu comuna</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Ciudad *</label>
                            <input x-model="ciudad" type="text" value="Santiago" placeholder="Santiago" class="form-input">
                        </div>
                    </div>
                    <div class="mt-5 flex justify-end">
                        <button @click="goStep2()" class="btn-primary px-8 py-3">
                            Continuar →
                        </button>
                    </div>
                </div>
            </div>

            {{-- Step 2: Despacho --}}
            <div x-show="step === 2" x-transition>
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-[#0A3D7A] mb-5 flex items-center gap-2" style="font-family:'Poppins',sans-serif;">
                        <span class="w-7 h-7 step-active rounded-full flex items-center justify-center text-sm">2</span>
                        Método de despacho
                    </h2>
                    <div class="space-y-3">
                        @foreach([
                            ['standard', 'Despacho Estándar', 'Entrega en 24–48 horas hábiles', '$2.500 (Gratis sobre $15.000)', 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
                            ['express',  'Despacho Express',  'Entrega el mismo día (antes 12h)', '$3.990', 'M13 10V3L4 14h7v7l9-11h-7z'],
                        ] as [$val,$title,$sub,$price,$ico])
                        <label class="flex items-center gap-4 p-4 border-2 rounded-xl cursor-pointer transition-all"
                               :class="shipping === '{{ $val }}' ? 'border-[#1a56c4] bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="shipping" value="{{ $val }}" x-model="shipping" class="text-[#1a56c4]">
                            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                                <svg class="w-4.5 h-4.5 text-[#1a56c4]" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $ico }}"/></svg>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-sm text-gray-800">{{ $title }}</div>
                                <div class="text-xs text-gray-500">{{ $sub }}</div>
                            </div>
                            <div class="text-sm font-bold text-[#0A3D7A]">{{ $price }}</div>
                        </label>
                        @endforeach
                    </div>

                    {{-- Coupon --}}
                    <div class="mt-6 pt-5 border-t border-gray-100">
                        <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">Cupón de descuento</label>
                        <div class="flex gap-2">
                            <input x-model="coupon" type="text" placeholder="Ej: PROMO10" class="form-input uppercase" @keydown.enter.prevent="validateCoupon()">
                            <button @click="validateCoupon()" class="btn-outline whitespace-nowrap text-sm py-2.5 px-4">Aplicar</button>
                        </div>
                        <p x-show="couponMsg" x-text="couponMsg"
                           :class="couponValid ? 'text-green-600' : 'text-red-500'"
                           class="text-xs mt-1.5 font-medium"></p>
                    </div>

                    <div class="mt-5 flex justify-between">
                        <button @click="step = 1; touched = false" class="btn-outline text-sm py-2.5 px-5">← Volver</button>
                        <button @click="step = 3; $nextTick(() => window.scrollTo({top:0,behavior:'smooth'}))"
                            class="btn-primary px-8 py-3">Continuar →</button>
                    </div>
                </div>
            </div>

            {{-- Step 3: Pago --}}
            <div x-show="step === 3" x-transition>
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-[#0A3D7A] mb-5 flex items-center gap-2" style="font-family:'Poppins',sans-serif;">
                        <span class="w-7 h-7 step-active rounded-full flex items-center justify-center text-sm">3</span>
                        Método de pago
                    </h2>
                    <div class="space-y-3 mb-6">
                        @foreach([
                            ['webpay',        'Webpay Plus',            'Tarjeta de débito o crédito (pago inmediato)',   'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',  '#1a56c4'],
                            ['transferencia', 'Transferencia Bancaria', 'Banco de Chile · Cuenta corriente',              'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z',                       '#0A3D7A'],
                            ['contra_entrega','Contra Entrega',         'Paga en efectivo o con POS al recibir tu pedido','M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', '#d97706'],
                            ['mercadopago',  'MercadoPago',            'Débito, crédito y cuotas sin interés',           'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 16v-1m0-14.5V7m0 0a2.5 2.5 0 110 5 2.5 2.5 0 010-5z', '#059669'],
                            ['whatsapp',     'WhatsApp',               'Coordina el pago directamente con nosotros',     'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z',                                  '#25D366'],
                        ] as [$val, $title, $sub, $ico, $color])
                        <label class="flex items-center gap-4 p-4 border-2 rounded-xl cursor-pointer transition-all"
                               :class="payment === '{{ $val }}' ? 'border-[#1a56c4] bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="payment" value="{{ $val }}" x-model="payment" class="text-[#1a56c4]">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0" style="background:{{ $color }}18;">
                                <svg style="width:18px;height:18px;color:{{ $color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $ico }}"/></svg>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-sm text-gray-800">{{ $title }}</div>
                                <div class="text-xs text-gray-500">{{ $sub }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    {{-- Info contextual según método de pago --}}
                    <div x-show="payment === 'transferencia'" class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-[#0A3D7A] mb-5">
                        <p class="font-semibold mb-2 flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg> Datos para transferencia:</p>
                        <div class="space-y-0.5 text-xs text-gray-700">
                            <p><strong>Banco:</strong> Banco de Chile</p>
                            <p><strong>Tipo:</strong> Cuenta Corriente &nbsp;·&nbsp; <strong>N°:</strong> 00-12345-67</p>
                            <p><strong>Titular:</strong> Aguas Santa Catalina SpA &nbsp;·&nbsp; <strong>RUT:</strong> 76.890.123-K</p>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Envía el comprobante a <strong>pagos@aguassantacatalina.cl</strong> indicando tu número de pedido.</p>
                    </div>

                    <div x-show="payment === 'contra_entrega'" class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm mb-5">
                        <p class="font-semibold text-amber-800 mb-1">¿Cómo funciona el pago contra entrega?</p>
                        <ul class="text-xs text-amber-700 space-y-1 list-disc list-inside">
                            <li>El repartidor lleva un POS para pago con tarjeta o puedes pagar en efectivo.</li>
                            <li>El monto exacto se confirma por WhatsApp al coordinar la entrega.</li>
                            <li>Disponible solo para pedidos dentro del Gran Santiago.</li>
                        </ul>
                    </div>

                    <div x-show="payment === 'whatsapp'" class="bg-green-50 border border-green-200 rounded-xl p-4 text-sm mb-5">
                        <p class="font-semibold text-green-800 mb-1 flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z"/></svg> Pedido por WhatsApp</p>
                        <p class="text-xs text-green-700">Confirma tu pedido y coordina el pago directamente con nuestro equipo. Te responderemos en minutos.</p>
                    </div>

                    <div class="flex justify-between">
                        <button @click="step = 2" class="btn-outline text-sm py-2.5 px-5">← Volver</button>
                        <button @click="submitOrder()" class="btn-primary text-base px-10 py-3.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            CONFIRMAR PEDIDO
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── ORDER SUMMARY (sticky) ───────────────────────────────────────── --}}
        <div class="card p-6 sticky top-24">
            <h2 class="text-base font-bold text-[#0A3D7A] mb-4" style="font-family:'Poppins',sans-serif;">Resumen</h2>
            <div class="max-h-60 overflow-y-auto space-y-3 mb-4">
                <template x-for="item in $store.cart.items" :key="item.id">
                    <div class="flex items-center gap-3 text-sm">
                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-[#1a56c4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-800 truncate" x-text="item.name"></div>
                            <div class="text-gray-400 text-xs" x-text="`× ${item.quantity}`"></div>
                        </div>
                        <span class="font-semibold text-[#0A3D7A] shrink-0" x-text="`$${(item.price * item.quantity).toLocaleString('es-CL')}`"></span>
                    </div>
                </template>
            </div>
            <div class="space-y-2 py-3 border-y border-gray-100 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span x-text="`$${subtotal.toLocaleString('es-CL')}`" class="font-medium"></span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Despacho</span>
                    <span x-show="shippingCost === 0" class="text-[#1FA855] font-semibold">Gratis</span>
                    <span x-show="shippingCost > 0" x-text="`$${shippingCost.toLocaleString('es-CL')}`" class="font-medium"></span>
                </div>
                <div x-show="couponDiscount > 0" class="flex justify-between text-green-600">
                    <span>Descuento</span>
                    <span x-text="`-$${couponDiscount.toLocaleString('es-CL')}`" class="font-semibold"></span>
                </div>
            </div>
            <div class="flex justify-between items-baseline pt-3">
                <span class="font-bold text-gray-800">Total</span>
                <span x-text="`$${total.toLocaleString('es-CL')}`" class="text-2xl font-black text-[#0A3D7A]" style="font-family:'Poppins',sans-serif;"></span>
            </div>

            {{-- Trust --}}
            <div class="mt-5 pt-4 border-t border-gray-100 space-y-2">
                @foreach([
                    ['M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'Pago 100% seguro'],
                    ['M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1.75 12.5A2 2 0 008.73 22h6.54a2 2 0 001.98-1.5L19 8', 'Despacho gratis sobre $15.000'],
                    ['M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'Soporte L–S 8–20h'],
                ] as [$path, $label])
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <svg class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/></svg>
                    {{ $label }}
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
