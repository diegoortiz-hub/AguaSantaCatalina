@extends('layouts.admin')
@section('title', 'Configuración')
@section('page-title', 'Configuración')

@section('content')

<div x-data="{ tab: 'general' }">

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800" style="font-family:'Poppins',sans-serif;">Configuración General</h1>
        <p class="text-xs text-slate-500 mt-0.5">Ajustes comerciales, cuentas bancarias, WhatsApp y cobertura de despacho</p>
    </div>
    <button type="submit" form="form-config"
            class="flex items-center gap-2 bg-[#1a56c4] hover:bg-[#0A3D7A] text-white px-5 py-2.5 text-sm font-bold rounded-xl shadow-md shadow-blue-500/20 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
        Guardar Cambios
    </button>
</div>

{{-- Tabs --}}
<div class="flex items-center gap-0 border-b border-slate-200 mb-6 overflow-x-auto">
    @foreach([
        ['general',    'Datos de la Tienda',   'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',    false],
        ['bancarios',  'Datos Bancarios',       'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',                                                          false],
        ['whatsapp',   'WhatsApp y Atención',   'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', false],
        ['despacho',   'Zonas de Despacho',     'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0', false],
        ['mantencion', 'Mantenimiento',         'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', !empty($settings['mantencion_activa'])],
    ] as [$key, $label, $ico, $alerta])
    <button @click="tab = '{{ $key }}'" type="button"
            :class="tab === '{{ $key }}'
                ? '{{ $alerta ? 'border-amber-500 text-amber-600' : 'border-[#1a56c4] text-[#1a56c4]' }}'
                : 'border-transparent text-slate-500 hover:text-slate-800'"
            class="px-4 py-3 text-xs font-bold border-b-2 transition-all flex items-center gap-2 whitespace-nowrap -mb-px">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $ico }}"/></svg>
        {{ $label }}
        @if($alerta && $key === 'mantencion')
        <span class="inline-flex items-center justify-center w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
        @endif
    </button>
    @endforeach
</div>

<form id="form-config" method="POST" action="{{ route('admin.configuracion.save') }}" class="space-y-6">
    @csrf

    {{-- Tab 1: Datos de la Tienda --}}
    <div x-show="tab === 'general'" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
        <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
            <svg class="w-5 h-5 text-[#1a56c4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <div>
                <h3 class="font-bold text-slate-900 text-base">Identidad Comercial</h3>
                <p class="text-xs text-slate-400">Datos visibles en boletas y correos transaccionales</p>
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            @foreach([
                ['empresa',  'Razón Social / Nombre Comercial', 'text', ''],
                ['rut',      'RUT Empresa',                     'text', 'font-mono'],
                ['email',    'Correo de Contacto',              'email', ''],
                ['telefono', 'Teléfono / Central',              'text', 'font-mono'],
            ] as [$name, $label, $type, $extra])
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ $label }}</label>
                <input type="{{ $type }}" name="{{ $name }}" value="{{ $settings[$name] ?? '' }}"
                       class="w-full px-3.5 py-2.5 text-sm {{ $extra }} rounded-xl border border-slate-200 focus:border-[#1a56c4] outline-none">
            </div>
            @endforeach
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Dirección Planta / Oficina</label>
                <input type="text" name="direccion" value="{{ $settings['direccion'] ?? '' }}"
                       class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-[#1a56c4] outline-none">
            </div>
            @foreach([
                ['comuna',  'Comuna'],
                ['ciudad',  'Ciudad'],
                ['horario', 'Horario de Atención'],
            ] as [$name, $label])
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ $label }}</label>
                <input type="text" name="{{ $name }}" value="{{ $settings[$name] ?? '' }}"
                       class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-[#1a56c4] outline-none">
            </div>
            @endforeach
        </div>
    </div>

    {{-- Tab 2: Datos Bancarios --}}
    <div x-show="tab === 'bancarios'" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
        <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
            <svg class="w-5 h-5 text-[#1a56c4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            <div>
                <h3 class="font-bold text-slate-900 text-base">Cuenta para Transferencias</h3>
                <p class="text-xs text-slate-400">Se muestra a clientes que eligen pagar con transferencia bancaria</p>
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            @foreach([
                ['banco',       'Banco',                          'text',  ''],
                ['tipo_cuenta', 'Tipo de Cuenta',                 'text',  ''],
                ['nro_cuenta',  'Número de Cuenta',               'text',  'font-mono'],
                ['titular',     'Titular de la Cuenta',           'text',  ''],
                ['rut_titular', 'RUT Titular',                    'text',  'font-mono'],
                ['email_pagos', 'Email para Comprobantes de Pago','email', ''],
            ] as [$name, $label, $type, $extra])
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ $label }}</label>
                <input type="{{ $type }}" name="{{ $name }}" value="{{ $settings[$name] ?? '' }}"
                       class="w-full px-3.5 py-2.5 text-sm {{ $extra }} rounded-xl border border-slate-200 focus:border-[#1a56c4] outline-none">
            </div>
            @endforeach
        </div>
    </div>

    {{-- Tab 3: WhatsApp --}}
    <div x-show="tab === 'whatsapp'" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
        <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
            <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            <div>
                <h3 class="font-bold text-slate-900 text-base">Canal de Ventas y Atención WhatsApp</h3>
                <p class="text-xs text-slate-400">Número oficial para pedidos directos y atención al cliente</p>
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Número de WhatsApp (con código de país, sin +)</label>
            <div class="flex items-center gap-3">
                <input type="text" name="whatsapp" value="{{ $settings['whatsapp'] ?? '' }}"
                       class="w-full max-w-xs px-3.5 py-2.5 text-base font-mono font-bold rounded-xl border border-slate-200 focus:border-[#1a56c4] outline-none">
                <a href="https://wa.me/{{ $settings['whatsapp'] ?? '' }}" target="_blank"
                   class="flex items-center gap-1.5 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-colors shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Probar enlace
                </a>
            </div>
        </div>
        <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <div class="text-xs text-emerald-900 space-y-1">
                <p class="font-bold">Integración de Botón Flotante Activa</p>
                <p class="text-emerald-700">Los clientes pueden iniciar conversaciones precargando su carrito o solicitando recargas de bidones con un toque.</p>
            </div>
        </div>
    </div>

    {{-- Tab 4: Zonas de Despacho --}}
    <div x-show="tab === 'despacho'" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
        <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
            <svg class="w-5 h-5 text-[#1a56c4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
            <div>
                <h3 class="font-bold text-slate-900 text-base">Zonas y Tarifas de Despacho</h3>
                <p class="text-xs text-slate-400">Costo de envío y umbral de despacho gratuito</p>
            </div>
        </div>
        <div class="max-w-xs">
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Monto Mínimo para Despacho Gratis (CLP $)</label>
            <input type="number" name="despacho_gratis" value="{{ $settings['despacho_gratis'] ?? 30000 }}" min="0"
                   class="w-full px-3.5 py-2.5 text-sm font-mono font-bold rounded-xl border border-slate-200 focus:border-[#1a56c4] outline-none">
        </div>

        <div class="border border-slate-100 rounded-2xl overflow-hidden shadow-sm">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-mono uppercase text-[11px] border-b border-slate-100">
                        <th class="p-3.5">Zona / Comunas</th>
                        <th class="p-3.5">Tiempo estimado</th>
                        <th class="p-3.5">Costo (CLP)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach([
                        ['Santiago Centro, Providencia, Ñuñoa', '2–4 horas el mismo día', 1990],
                        ['Las Condes, Vitacura, Lo Barnechea',  '2–4 horas el mismo día', 2490],
                        ['Maipú, Puente Alto, La Florida',      '24 horas hábiles',        2990],
                        ['Resto Región Metropolitana',          '24–48 horas',             3990],
                    ] as [$zona, $tiempo, $costo])
                    <tr class="hover:bg-slate-50/50">
                        <td class="p-3.5 font-medium text-slate-800">{{ $zona }}</td>
                        <td class="p-3.5 text-slate-500">{{ $tiempo }}</td>
                        <td class="p-3.5">
                            <span class="font-mono font-bold text-slate-700">${{ number_format($costo, 0, ',', '.') }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="text-xs text-slate-400">Para modificar zonas de despacho detalladas, edita la tabla de comunas en la base de datos o contacta al desarrollador.</p>
    </div>

    {{-- Tab 5: Mantenimiento --}}
    <div x-show="tab === 'mantencion'" class="space-y-5">

        {{-- Estado actual --}}
        @php $enMantencion = !empty($settings['mantencion_activa']); @endphp
        <div class="rounded-2xl border p-5 flex items-start gap-4
            {{ $enMantencion ? 'bg-amber-50 border-amber-300' : 'bg-white border-slate-200/80 shadow-sm' }}">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                {{ $enMantencion ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 text-slate-500' }}">
                @if($enMantencion)
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                @else
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @endif
            </div>
            <div>
                <p class="font-bold text-sm {{ $enMantencion ? 'text-amber-800' : 'text-slate-800' }}">
                    {{ $enMantencion ? 'La tienda está en modo mantenimiento' : 'La tienda está en línea y funcionando' }}
                </p>
                <p class="text-xs mt-0.5 {{ $enMantencion ? 'text-amber-600' : 'text-slate-400' }}">
                    {{ $enMantencion ? 'Los visitantes ven la página de mantenimiento. El panel admin sigue accesible.' : 'Los clientes pueden ver y comprar productos normalmente.' }}
                </p>
            </div>
        </div>

        {{-- Controles --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                <svg class="w-5 h-5 text-[#1a56c4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Modo Mantenimiento</h3>
                    <p class="text-xs text-slate-400">Activa para mostrar una página especial a los visitantes mientras trabajas</p>
                </div>
            </div>

            {{-- Toggle --}}
            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200">
                <div>
                    <p class="text-sm font-semibold text-slate-800">Activar modo mantenimiento</p>
                    <p class="text-xs text-slate-400 mt-0.5">Los administradores siguen viendo la tienda normal</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="mantencion_activa" value="0">
                    <input type="checkbox" name="mantencion_activa" value="1" class="sr-only peer"
                           {{ !empty($settings['mantencion_activa']) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer
                                peer-checked:after:translate-x-full peer-checked:bg-amber-500
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                </label>
            </div>

            {{-- Mensaje --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Mensaje para los visitantes</label>
                <textarea name="mantencion_mensaje" rows="3"
                          class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm outline-none focus:border-[#1a56c4] focus:ring-1 focus:ring-[#1a56c4]/20 resize-none"
                          placeholder="Estamos realizando mejoras. Volvemos pronto. ¡Gracias por tu paciencia!">{{ $settings['mantencion_mensaje'] ?? '' }}</textarea>
                <p class="text-xs text-slate-400 mt-1">Este texto aparece en la página de mantenimiento.</p>
            </div>

            {{-- Hora estimada --}}
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Hora estimada de regreso (opcional)</label>
                <input type="datetime-local" name="mantencion_fin"
                       value="{{ $settings['mantencion_fin'] ?? '' }}"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm outline-none focus:border-[#1a56c4]">
                <p class="text-xs text-slate-400 mt-1">Si se especifica, se mostrará al visitante como hora estimada de vuelta.</p>
            </div>
        </div>

        {{-- Info box --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-[#0A3D7A]">
            <p class="font-semibold mb-1">¿Cómo funciona?</p>
            <ul class="text-xs text-blue-700 space-y-1 list-disc list-inside">
                <li>Los visitantes ven una página animada con el mensaje que configures.</li>
                <li>El panel de administración en <code>/admin</code> sigue accesible siempre.</li>
                <li>Los administradores con sesión activa pueden ver la tienda normalmente.</li>
                <li>Para activar/desactivar, marca el interruptor y guarda los cambios.</li>
            </ul>
        </div>
    </div>

    {{-- Bottom CTA --}}
    <div class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Todos los cambios se aplicarán en tiempo real a la tienda
        </div>
        <button type="submit"
                class="flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white bg-[#1a56c4] hover:bg-[#0A3D7A] rounded-xl shadow-md shadow-blue-500/20 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
            Guardar Toda la Configuración
        </button>
    </div>
</form>

</div>

@endsection
