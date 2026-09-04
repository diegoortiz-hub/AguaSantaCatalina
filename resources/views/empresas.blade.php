@extends('layouts.app')

@section('title', 'Planes Empresas B2B — Aguas Santa Catalina')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-12">

    {{-- Hero --}}
    <div class="relative rounded-3xl overflow-hidden bg-slate-900 text-white min-h-[360px] flex items-center shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900/90 to-cyan-950/60"></div>
        <div class="absolute inset-0 opacity-20"
             style="background-image: url('https://images.unsplash.com/photo-1497366216548-37526070297c?w=1400&q=80'); background-size: cover; background-position: center;">
        </div>

        <div class="relative z-10 p-6 sm:p-12 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300 text-xs font-bold uppercase tracking-wider mb-4 border border-cyan-400/30">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>División Corporativa B2B</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight leading-tight">
                Planes de Agua Purificada para Empresas
            </h1>
            <p class="mt-3 text-sm sm:text-base text-slate-300 leading-relaxed">
                Abastecimiento continuo con Factura a 30 días, dispensadores en comodato sin costo, reposición express en menos de 24 horas y mantenimiento programado.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="#calculadora"
                   class="px-6 py-3 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs sm:text-sm tracking-wide shadow-md transition-all inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Calcular Presupuesto Mensual</span>
                </a>
                <a href="https://wa.me/56991493272?text=Hola%2C+soy+una+empresa+y+quiero+información+sobre+planes+B2B"
                   target="_blank" rel="noopener noreferrer"
                   class="px-5 py-3 rounded-xl bg-[#25D366] hover:bg-[#20b858] text-white font-bold text-xs sm:text-sm tracking-wide transition-all inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    <span>Hablar con Ejecutivo B2B</span>
                </a>
            </div>
        </div>
    </div>

    {{-- 4 Pilares de Valor --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
            <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Facturación a 30 Días</h3>
            <p class="text-xs text-slate-500 leading-relaxed">Emitimos Factura Electrónica exenta o afecta con orden de compra y condiciones de pago corporativo a 30 días.</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Reposición Express 24h</h3>
            <p class="text-xs text-slate-500 leading-relaxed">Ruta fija semanal o quincenal con stock de seguridad para que tu equipo nunca se quede sin agua pura.</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Comodato & Sanitización</h3>
            <p class="text-xs text-slate-500 leading-relaxed">Dispensadores de pedestal frío/calor sin costo de arriendo con sanitización preventiva semestral incluida.</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Ejecutivo Dedicado</h3>
            <p class="text-xs text-slate-500 leading-relaxed">Atención prioritaria y directa vía WhatsApp y teléfono para pedidos extraordinarios o mantención técnica.</p>
        </div>
    </div>

    {{-- Calculadora + Formulario --}}
    <div id="calculadora"
         x-data="{
            employees: 25,
            dispenserType: 'pedestal',
            quoteSubmitted: false,
            form: {
                companyName: '',
                rut: '',
                contactName: '',
                email: '',
                phone: '',
                commune: 'Providencia',
                notes: '',
                requestFreeDemo: true
            },
            get estimatedBottles() {
                const liters = this.employees * 25;
                return Math.max(2, Math.ceil(liters / 20));
            },
            get recommendedDispensers() {
                return Math.max(1, Math.ceil(this.employees / 15));
            },
            get unitPriceNeto() {
                if (this.estimatedBottles > 30) return 2990;
                if (this.estimatedBottles > 10) return 3290;
                return 3590;
            },
            get subtotalNeto() {
                const bottles = this.estimatedBottles * this.unitPriceNeto;
                const dispenser = this.dispenserType === 'conexion_red' ? this.recommendedDispensers * 18990 : 0;
                return bottles + dispenser;
            },
            get iva() { return Math.round(this.subtotalNeto * 0.19); },
            get totalConIva() { return this.subtotalNeto + this.iva; },
            get shippingProgress() {
                return Math.min(100, Math.round((this.employees / 200) * 100));
            },
            submitQuote() {
                const msg = encodeURIComponent(
                    'Hola, soy ' + this.form.contactName + ' de ' + this.form.companyName +
                    ' (RUT ' + this.form.rut + ').' +
                    ' Necesito cotización para ' + this.employees + ' colaboradores.' +
                    ' Dispensadores: ' + this.dispenserType + '.' +
                    ' Total estimado: $' + this.totalConIva.toLocaleString('es-CL') + ' mensual.' +
                    (this.form.requestFreeDemo ? ' Solicito semana de prueba gratis.' : '') +
                    (this.form.notes ? ' Notas: ' + this.form.notes : '') +
                    ' Contacto: ' + this.form.email + ' / ' + this.form.phone
                );
                window.open('https://wa.me/56991493272?text=' + msg, '_blank');
                this.quoteSubmitted = true;
            }
         }"
         class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- Calculadora Interactiva --}}
        <div class="lg:col-span-6 bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
            <div>
                <div class="flex items-center gap-2 text-cyan-600 text-xs font-bold uppercase tracking-wider">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Simulador de Consumo</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mt-1">Calcula el plan ideal para tu oficina</h2>
                <p class="text-xs text-slate-500 mt-1">Mueve el selector para ver la estimación de bidones y costo mensual neto estimado.</p>
            </div>

            {{-- Slider de colaboradores --}}
            <div class="space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-700">Número de colaboradores:</span>
                    <span class="text-lg font-black text-cyan-700 bg-white px-3 py-1 rounded-xl border border-slate-200"
                          x-text="employees + ' personas'"></span>
                </div>
                <input type="range" min="5" max="200" step="5" x-model.number="employees"
                       class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-cyan-600">
                <div class="flex justify-between text-[10px] text-slate-400">
                    <span>5 colaboradores</span>
                    <span>100</span>
                    <span>200+ colaboradores</span>
                </div>
            </div>

            {{-- Tipo de dispensador --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">Tipo de dispensadores preferidos:</label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach([
                        ['value' => 'pedestal', 'label' => 'Pedestal Frío/Calor (Comodato)'],
                        ['value' => 'sobremesa', 'label' => 'Sobremesa Compacto'],
                        ['value' => 'conexion_red', 'label' => 'Conexión a Red Ósmosis'],
                    ] as $option)
                    <button type="button" @click="dispenserType = '{{ $option['value'] }}'"
                            :class="dispenserType === '{{ $option['value'] }}'
                                ? 'border-cyan-600 bg-cyan-50/70 text-cyan-900 ring-2 ring-cyan-500/20'
                                : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'"
                            class="p-3 rounded-xl border text-xs font-bold text-center cursor-pointer transition-all">
                        {{ $option['label'] }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Resultado calculadora --}}
            <div class="bg-cyan-50/60 border border-cyan-200 rounded-2xl p-5 space-y-3">
                <h4 class="text-xs font-bold uppercase tracking-wider text-cyan-900">Estimación de Consumo Mensual Recomendado:</h4>
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="bg-white p-3 rounded-xl border border-cyan-100">
                        <span class="text-slate-500 block">Bidones 20L / mes:</span>
                        <strong class="text-base text-slate-900" x-text="estimatedBottles + ' bidones'"></strong>
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-cyan-100">
                        <span class="text-slate-500 block">Dispensadores sugeridos:</span>
                        <strong class="text-base text-slate-900" x-text="recommendedDispensers + ' equipos'"></strong>
                    </div>
                </div>
                <div class="pt-2 border-t border-cyan-200/60 space-y-1 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal Neto Estimado:</span>
                        <span class="font-semibold" x-text="'$' + subtotalNeto.toLocaleString('es-CL')"></span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>IVA (19%):</span>
                        <span x-text="'$' + iva.toLocaleString('es-CL')"></span>
                    </div>
                    <div class="flex justify-between text-sm font-extrabold text-slate-900 pt-1 border-t border-cyan-200">
                        <span>Total Estimado Mensual (con IVA):</span>
                        <span class="text-cyan-700" x-text="'$' + totalConIva.toLocaleString('es-CL')"></span>
                    </div>
                </div>
            </div>

            {{-- Clientes logos --}}
            <div class="pt-4 border-t border-slate-100">
                <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-3 text-center">Empresas que confían en nosotros</p>
                <div class="flex flex-wrap justify-center gap-x-6 gap-y-2">
                    @foreach(['COPEC','Cencosud','Walmart','Nestlé','UNIMARC','Sodexo','Falabella','CCU'] as $logo)
                    <span class="text-xs font-bold text-slate-400 tracking-wide">{{ $logo }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Formulario de Cotización --}}
        <div class="lg:col-span-6 bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-sm">
            <div x-show="!quoteSubmitted">
                <div class="mb-5">
                    <span class="text-xs font-bold uppercase tracking-wider text-cyan-600">Solicitud de Cotización</span>
                    <h3 class="text-xl font-bold text-slate-800 mt-0.5">Recibe una propuesta formal en 2 horas</h3>
                    <p class="text-xs text-slate-500 mt-1">Incluye la opción de solicitar 1 semana de prueba gratis con dispensador sin compromiso.</p>
                </div>

                <form @submit.prevent="submitQuote()" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Razón Social o Empresa *</label>
                            <input type="text" required x-model="form.companyName"
                                   placeholder="Empresa SpA"
                                   class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">RUT Empresa *</label>
                            <input type="text" required x-model="form.rut"
                                   placeholder="76.123.456-7"
                                   class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nombre Contacto *</label>
                            <input type="text" required x-model="form.contactName"
                                   placeholder="Carolina Soto"
                                   class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Correo Corporativo *</label>
                            <input type="email" required x-model="form.email"
                                   placeholder="contacto@empresa.cl"
                                   class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Teléfono Directo *</label>
                            <input type="tel" required x-model="form.phone"
                                   placeholder="+56 9 8765 4321"
                                   class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Comuna Oficina *</label>
                            <select x-model="form.commune"
                                    class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-cyan-500 font-semibold">
                                @foreach([
                                    'Las Condes','Providencia','Vitacura','Lo Barnechea','Ñuñoa',
                                    'Santiago Centro','La Reina','Peñalolén','La Florida','Macul',
                                    'San Miguel','Maipú','Huechuraba','Quilicura','Colina / Chicureo','Puente Alto'
                                ] as $commune)
                                <option value="{{ $commune }}">{{ $commune }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Demo gratis --}}
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl">
                        <label class="flex items-start gap-2.5 cursor-pointer text-xs">
                            <input type="checkbox" x-model="form.requestFreeDemo"
                                   class="mt-0.5 text-emerald-600 rounded focus:ring-emerald-500">
                            <span class="font-semibold text-emerald-900">
                                Deseo solicitar 1 semana de prueba gratis (dispensador y bidón de cortesía para mi equipo).
                            </span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Notas o Requerimientos Especiales (Opcional)</label>
                        <textarea rows="2" x-model="form.notes"
                                  placeholder="Ej: Tenemos 2 pisos en Providencia y necesitamos 2 dispensadores..."
                                  class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-cyan-500"></textarea>
                    </div>

                    <button type="submit"
                            class="w-full py-3.5 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs sm:text-sm tracking-wide shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        <span>SOLICITAR COTIZACIÓN FORMAL (<span x-text="employees"></span> PERSONAS)</span>
                    </button>
                </form>
            </div>

            {{-- Confirmación enviada --}}
            <div x-show="quoteSubmitted" x-cloak class="text-center py-10 space-y-4">
                <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800">¡Cotización enviada por WhatsApp!</h3>
                <p class="text-xs text-slate-600 max-w-md mx-auto">
                    Un ejecutivo comercial revisará los requerimientos de <strong x-text="form.companyName"></strong> y te contactará a <strong x-text="form.email"></strong> en menos de 2 horas hábiles.
                </p>
                <button @click="quoteSubmitted = false"
                        class="px-5 py-2 bg-slate-800 text-white font-bold text-xs rounded-xl cursor-pointer">
                    Generar otra cotización
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
