@extends('layouts.app')

@section('title', 'Quiénes Somos — Aguas Santa Catalina')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 space-y-14">

    {{-- Hero --}}
    <div class="text-center space-y-4">
        <span class="inline-block px-3 py-1 rounded-full bg-cyan-100 text-cyan-700 text-xs font-bold uppercase tracking-wider">Nuestra Historia</span>
        <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight">Aguas Santa Catalina</h1>
        <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
            Más de 15 años llevando agua purificada de alta calidad a hogares y empresas de Santiago, con un servicio cercano y confiable.
        </p>
    </div>

    {{-- Misión / Visión / Valores --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-cyan-50 border border-cyan-200 rounded-2xl p-6 space-y-3">
            <div class="w-10 h-10 rounded-xl bg-cyan-600 text-white flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <h3 class="font-bold text-slate-800">Nuestra Misión</h3>
            <p class="text-xs text-slate-600 leading-relaxed">Proveer agua purificada de la más alta calidad con un servicio de despacho puntual, trato cercano y precios justos para mejorar el bienestar de las personas.</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 space-y-3">
            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </div>
            <h3 class="font-bold text-slate-800">Nuestra Visión</h3>
            <p class="text-xs text-slate-600 leading-relaxed">Ser el proveedor de agua purificada más confiable y recomendado de Santiago, expandiendo nuestra cobertura a todas las comunas de la Región Metropolitana.</p>
        </div>
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 space-y-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </div>
            <h3 class="font-bold text-slate-800">Nuestros Valores</h3>
            <p class="text-xs text-slate-600 leading-relaxed">Calidad, puntualidad, honestidad y compromiso con el medioambiente. Reciclamos cada bidón y trabajamos con procesos de purificación certificados.</p>
        </div>
    </div>

    {{-- Estadísticas --}}
    <div class="bg-gradient-to-r from-cyan-700 to-blue-800 rounded-3xl p-8 sm:p-12 text-white">
        <h2 class="text-xl font-bold text-center mb-8 text-cyan-100">Números que nos respaldan</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
            @foreach([
                ['stat' => '+15', 'label' => 'Años de experiencia'],
                ['stat' => '+3.000', 'label' => 'Clientes activos'],
                ['stat' => '+50.000', 'label' => 'Bidones entregados'],
                ['stat' => '16', 'label' => 'Comunas de cobertura'],
            ] as $item)
            <div>
                <div class="text-3xl sm:text-4xl font-extrabold text-white">{{ $item['stat'] }}</div>
                <div class="text-xs text-cyan-200 mt-1">{{ $item['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- CTA --}}
    <div class="text-center space-y-4">
        <h2 class="text-2xl font-bold text-slate-800">¿Listo para probar nuestra agua?</h2>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ route('catalogo') }}" class="px-6 py-3 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-sm tracking-wide transition-all">
                Ver Catálogo
            </a>
            <a href="{{ route('empresas') }}" class="px-6 py-3 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-sm tracking-wide transition-all">
                Planes Empresas
            </a>
        </div>
    </div>

</div>
@endsection
