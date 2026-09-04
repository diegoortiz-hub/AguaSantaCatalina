@extends('layouts.app')

@section('title', 'Contacto — Aguas Santa Catalina')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 space-y-12">

    <div class="text-center space-y-3">
        <span class="inline-block px-3 py-1 rounded-full bg-cyan-100 text-cyan-700 text-xs font-bold uppercase tracking-wider">Contáctanos</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">¿Cómo podemos ayudarte?</h1>
        <p class="text-slate-600 text-sm max-w-xl mx-auto">Estamos disponibles de lunes a sábado. La forma más rápida de contactarnos es por WhatsApp.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- Canales de contacto --}}
        <div class="space-y-4">
            <a href="https://wa.me/56991493272" target="_blank" rel="noopener noreferrer"
               class="flex items-center gap-4 p-5 bg-[#25D366]/10 border border-[#25D366]/30 rounded-2xl hover:bg-[#25D366]/20 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-[#25D366] text-white flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-slate-800 group-hover:text-green-700 transition-colors">WhatsApp — Respuesta inmediata</div>
                    <div class="text-xs text-slate-500">+56 9 9149 3272 · Lunes a Sábado 8:00 – 20:00</div>
                </div>
            </a>

            <div class="flex items-center gap-4 p-5 bg-slate-50 border border-slate-200 rounded-2xl">
                <div class="w-12 h-12 rounded-xl bg-cyan-600 text-white flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-slate-800">Correo Electrónico</div>
                    <div class="text-xs text-slate-500">contacto@aguassantacatalina.cl</div>
                    <div class="text-xs text-slate-400">Respuesta en menos de 24 horas hábiles</div>
                </div>
            </div>

            <div class="flex items-center gap-4 p-5 bg-slate-50 border border-slate-200 rounded-2xl">
                <div class="w-12 h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-slate-800">Cobertura de Despacho</div>
                    <div class="text-xs text-slate-500">16 comunas de Santiago</div>
                    <div class="text-xs text-slate-400">Las Condes, Providencia, Vitacura y más</div>
                </div>
            </div>

            <div class="flex items-center gap-4 p-5 bg-slate-50 border border-slate-200 rounded-2xl">
                <div class="w-12 h-12 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-slate-800">Horario de Atención</div>
                    <div class="text-xs text-slate-500">Lunes a Viernes: 8:00 – 19:00</div>
                    <div class="text-xs text-slate-400">Sábado: 9:00 – 14:00</div>
                </div>
            </div>
        </div>

        {{-- Formulario rápido --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm"
             x-data="{ sent: false, name: '', email: '', msg: '',
                submitContact() {
                    const text = encodeURIComponent('Hola, me llamo ' + this.name + ' (' + this.email + '). ' + this.msg);
                    window.open('https://wa.me/56991493272?text=' + text, '_blank');
                    this.sent = true;
                }
             }">
            <div x-show="!sent">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Escríbenos directamente</h3>
                <form @submit.prevent="submitContact()" class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nombre</label>
                        <input type="text" required x-model="name" placeholder="Tu nombre"
                               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:bg-white focus:border-cyan-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Correo</label>
                        <input type="email" required x-model="email" placeholder="tu@correo.cl"
                               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:bg-white focus:border-cyan-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Mensaje</label>
                        <textarea rows="4" required x-model="msg" placeholder="¿En qué podemos ayudarte?"
                                  class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:bg-white focus:border-cyan-500"></textarea>
                    </div>
                    <button type="submit"
                            class="w-full py-3 rounded-xl bg-[#25D366] hover:bg-[#20b858] text-white font-bold text-sm tracking-wide transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Enviar por WhatsApp
                    </button>
                </form>
            </div>
            <div x-show="sent" x-cloak class="text-center py-8 space-y-3">
                <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-sm font-bold text-slate-800">¡Mensaje enviado por WhatsApp!</p>
                <p class="text-xs text-slate-500">Te responderemos a la brevedad.</p>
                <button @click="sent = false" class="text-xs text-cyan-600 underline">Enviar otro mensaje</button>
            </div>
        </div>
    </div>

</div>
@endsection
