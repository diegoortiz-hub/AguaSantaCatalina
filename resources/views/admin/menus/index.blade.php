@extends('layouts.admin')
@section('title', 'Menús')
@section('page-title', 'Menús')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800" style="font-family:'Poppins',sans-serif;">Menús del sitio</h1>
    <p class="text-sm text-slate-400 mt-0.5">Los enlaces del menú principal y de las columnas del footer. El orden de la tienda se actualiza al guardar.</p>
</div>

@if(session('success'))
<div class="mb-5 flex items-start gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
    <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <span>{{ session('success') }}</span>
</div>
@endif

@if($errors->any())
<div class="mb-5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl px-4 py-3 text-sm">
    <ul class="space-y-1">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

@php
    // Se define una vez y se reutiliza en el formulario de alta y en cada edición.
    $rutas      = \App\Models\MenuItem::RUTAS_PERMITIDAS;
    $ubicaciones = \App\Models\MenuItem::UBICACIONES;
@endphp

{{-- Selector de destino reutilizable --}}
@php
$campoDestino = function ($item = null) use ($rutas, $paginas, $categorias) {
    return view('admin.menus._destino', [
        'item'       => $item,
        'rutas'      => $rutas,
        'paginas'    => $paginas,
        'categorias' => $categorias,
    ])->render();
};
@endphp

<div class="space-y-4">
@foreach($ubicaciones as $clave => $nombre)
@php $enlaces = $menus[$clave] ?? collect(); @endphp

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden"
     x-data="{ agregando: false, editando: null }">

    <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 bg-slate-50/60">
        <div>
            <h2 class="font-bold text-slate-800 text-base">{{ $nombre }}</h2>
            <p class="text-xs text-slate-400 mt-0.5">
                {{ $enlaces->count() }} {{ $enlaces->count() === 1 ? 'enlace' : 'enlaces' }}
                @if($clave === 'footer_3') · franja inferior del footer @endif
            </p>
        </div>
        <button type="button" @click="agregando = !agregando"
                class="flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold border border-slate-200 rounded-xl hover:border-[#0A3D7A] hover:text-[#0A3D7A] transition bg-white">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Agregar enlace
        </button>
    </div>

    {{-- Alta --}}
    <div x-show="agregando" x-cloak class="px-5 py-4 bg-blue-50/50 border-b border-slate-100">
        <form method="POST" action="{{ route('admin.menus.store') }}" class="grid sm:grid-cols-12 gap-3 items-end">
            @csrf
            <input type="hidden" name="ubicacion" value="{{ $clave }}">
            <div class="sm:col-span-4">
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Texto del enlace *</label>
                <input type="text" name="etiqueta" required maxlength="60" placeholder="Preguntas Frecuentes"
                       class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#1a56c4] outline-none">
            </div>
            <div class="sm:col-span-6">
                {!! $campoDestino() !!}
            </div>
            <div class="sm:col-span-1">
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Orden</label>
                <input type="number" name="orden" value="{{ ($enlaces->max('orden') ?? 0) + 1 }}" min="0" max="999"
                       class="w-full px-2 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#1a56c4] outline-none">
            </div>
            <div class="sm:col-span-1">
                <button class="w-full px-3 py-2 bg-[#0A3D7A] text-white text-xs font-bold rounded-lg hover:bg-[#1a56c4] transition">Añadir</button>
            </div>
        </form>
    </div>

    {{-- Listado --}}
    <div class="divide-y divide-slate-100">
        @forelse($enlaces->sortBy('orden') as $item)
        @php $destinoUrl = $item->url(); @endphp

        <div class="px-5 py-3">
            <div class="flex flex-wrap items-center gap-3">
                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-500 text-[11px] font-bold flex items-center justify-center shrink-0">{{ $item->orden }}</span>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $item->etiqueta }}</p>
                    <p class="text-xs text-slate-400 truncate">
                        {{ $item->destinoLegible() }}
                        @if(! $destinoUrl)
                        <span class="text-rose-600 font-semibold">· destino no disponible, no se muestra en el sitio</span>
                        @endif
                    </p>
                </div>

                @if($item->activo)
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-green-100 text-green-700 shrink-0">Visible</span>
                @else
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-200 text-slate-600 shrink-0">Oculto</span>
                @endif

                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="button" @click="editando = (editando === {{ $item->id }} ? null : {{ $item->id }})"
                            class="px-2.5 py-1.5 text-xs font-semibold border border-slate-200 rounded-lg hover:border-[#0A3D7A] hover:text-[#0A3D7A] transition">Editar</button>

                    <form method="POST" action="{{ route('admin.menus.toggle', $item) }}">
                        @csrf @method('PATCH')
                        <button class="px-2.5 py-1.5 text-xs font-semibold border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                            {{ $item->activo ? 'Ocultar' : 'Mostrar' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.menus.destroy', $item) }}"
                          onsubmit="return confirm('¿Eliminar el enlace «{{ $item->etiqueta }}»?')">
                        @csrf @method('DELETE')
                        <button class="px-2.5 py-1.5 text-xs font-semibold text-rose-600 border border-rose-200 rounded-lg hover:bg-rose-50 transition">Eliminar</button>
                    </form>
                </div>
            </div>

            {{-- Edición --}}
            <div x-show="editando === {{ $item->id }}" x-cloak class="mt-3 pt-3 border-t border-slate-100">
                <form method="POST" action="{{ route('admin.menus.update', $item) }}" class="grid sm:grid-cols-12 gap-3 items-end">
                    @csrf @method('PUT')
                    <input type="hidden" name="ubicacion" value="{{ $item->ubicacion }}">
                    <div class="sm:col-span-4">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Texto del enlace *</label>
                        <input type="text" name="etiqueta" required maxlength="60" value="{{ $item->etiqueta }}"
                               class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#1a56c4] outline-none">
                    </div>
                    <div class="sm:col-span-5">
                        {!! $campoDestino($item) !!}
                    </div>
                    <div class="sm:col-span-1">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Orden</label>
                        <input type="number" name="orden" value="{{ $item->orden }}" min="0" max="999"
                               class="w-full px-2 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#1a56c4] outline-none">
                    </div>
                    <div class="sm:col-span-2 flex items-center gap-2">
                        <label class="flex items-center gap-1.5 text-[11px] text-slate-600 cursor-pointer whitespace-nowrap">
                            <input type="checkbox" name="nueva_pestana" value="1" class="rounded border-slate-300" {{ $item->nueva_pestana ? 'checked' : '' }}>
                            Nueva pestaña
                        </label>
                        <input type="hidden" name="activo" value="{{ $item->activo ? 1 : 0 }}">
                        <button class="px-3 py-2 bg-[#0A3D7A] text-white text-xs font-bold rounded-lg hover:bg-[#1a56c4] transition">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="px-5 py-8 text-center">
            <p class="text-sm text-slate-400">Sin enlaces en esta ubicación.</p>
        </div>
        @endforelse
    </div>
</div>
@endforeach
</div>

@endsection
