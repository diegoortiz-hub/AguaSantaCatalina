@extends('layouts.app')

@section('title', $page->titulo)

@push('head')
@if($page->meta_descripcion)
<meta name="description" content="{{ $page->meta_descripcion }}">
@endif
@endpush

@section('content')

<div class="max-w-3xl mx-auto px-4 py-12 sm:py-16">

    <header class="mb-8 pb-6 border-b border-gray-100">
        <h1 class="text-3xl sm:text-4xl font-black text-[#0A3D7A] leading-tight" style="font-family:'Poppins',sans-serif;">
            {{ $page->titulo }}
        </h1>
        @if($page->bajada)
        <p class="text-base text-gray-500 mt-3">{{ $page->bajada }}</p>
        @endif
        <p class="text-xs text-gray-400 mt-4">
            Actualizado el {{ $page->updated_at->isoFormat('D [de] MMMM [de] YYYY') }}
        </p>
    </header>

    <div class="contenido-pagina">
        {!! $page->contenido !!}
    </div>

    <div class="mt-12 pt-6 border-t border-gray-100">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#1a56c4] hover:text-[#0A3D7A] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Volver al inicio
        </a>
    </div>
</div>

@push('head')
<style>
    .contenido-pagina { color:#475569; font-size:15px; line-height:1.75; }
    .contenido-pagina > * + * { margin-top:1.1em; }
    .contenido-pagina h2 { font-family:'Poppins',sans-serif; font-size:20px; font-weight:700; color:#0A3D7A; margin-top:2em; }
    .contenido-pagina h3 { font-family:'Poppins',sans-serif; font-size:16px; font-weight:600; color:#1e293b; margin-top:1.6em; }
    .contenido-pagina strong { color:#1e293b; font-weight:600; }
    .contenido-pagina a { color:#1a56c4; text-decoration:underline; }
    .contenido-pagina a:hover { color:#0A3D7A; }
    .contenido-pagina ul, .contenido-pagina ol { padding-left:1.4em; }
    .contenido-pagina ul { list-style:disc; }
    .contenido-pagina ol { list-style:decimal; }
    .contenido-pagina li + li { margin-top:.4em; }
    .contenido-pagina table { width:100%; border-collapse:collapse; font-size:14px; }
    .contenido-pagina th, .contenido-pagina td { border:1px solid #e2e8f0; padding:8px 10px; text-align:left; }
    .contenido-pagina th { background:#f8fafc; font-weight:600; color:#1e293b; }
</style>
@endpush
@endsection
