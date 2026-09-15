@extends('layouts.admin')
@section('title', $page->exists ? 'Editar página' : 'Nueva página')
@section('page-title', 'Páginas')

@section('content')

@php $editando = $page->exists; @endphp

<form method="POST" action="{{ $editando ? route('admin.paginas.update', $page) : route('admin.paginas.store') }}"
      x-data="{ slug: @js(old('slug', $page->slug)), titulo: @js(old('titulo', $page->titulo)) }">
    @csrf
    @if($editando) @method('PUT') @endif

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800" style="font-family:'Poppins',sans-serif;">
                {{ $editando ? 'Editar página' : 'Nueva página' }}
            </h1>
            <p class="text-sm text-slate-400 mt-0.5">
                @if($editando)
                Visible en <a href="{{ url('/'.$page->slug) }}" target="_blank" rel="noopener" class="font-mono text-[#1a56c4] hover:underline">/{{ $page->slug }}</a>
                @else
                La dirección se genera desde el título si la dejas vacía.
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.paginas.index') }}"
               class="px-4 py-2.5 text-sm font-semibold border border-slate-200 rounded-xl hover:bg-slate-50 transition">Cancelar</a>
            <button type="submit"
                    class="flex items-center gap-2 px-5 py-2.5 bg-[#0A3D7A] text-white text-sm font-semibold rounded-xl hover:bg-[#1a56c4] transition-colors shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Guardar
            </button>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl px-4 py-3 text-sm">
        <ul class="space-y-1">
            @foreach($errors->all() as $error)
            <li class="flex items-start gap-2">
                <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ $error }}</span>
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-4">

        {{-- Contenido --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Título *</label>
                    <input type="text" name="titulo" x-model="titulo" required maxlength="191"
                           value="{{ old('titulo', $page->titulo) }}"
                           class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-[#1a56c4] outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Bajada</label>
                    <input type="text" name="bajada" maxlength="255"
                           value="{{ old('bajada', $page->bajada) }}"
                           placeholder="Una línea breve bajo el título"
                           class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-[#1a56c4] outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Contenido</label>
                    <textarea name="contenido" rows="18"
                              class="w-full px-3.5 py-2.5 text-sm font-mono rounded-xl border border-slate-200 focus:border-[#1a56c4] outline-none leading-relaxed"
                              placeholder="<p>Texto del primer párrafo.</p>">{{ old('contenido', $page->contenido) }}</textarea>
                    <p class="text-[11px] text-slate-400 mt-1.5">
                        Admite HTML simple: <code class="bg-slate-100 px-1 rounded">&lt;p&gt;</code>,
                        <code class="bg-slate-100 px-1 rounded">&lt;h2&gt;</code>,
                        <code class="bg-slate-100 px-1 rounded">&lt;ul&gt;&lt;li&gt;</code>,
                        <code class="bg-slate-100 px-1 rounded">&lt;strong&gt;</code>,
                        <code class="bg-slate-100 px-1 rounded">&lt;a href&gt;</code> y tablas.
                    </p>
                </div>
            </div>
        </div>

        {{-- Ajustes --}}
        <div class="space-y-4">
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Dirección</label>
                    <div class="flex items-center rounded-xl border border-slate-200 overflow-hidden focus-within:border-[#1a56c4]">
                        <span class="px-2.5 py-2.5 text-xs text-slate-400 bg-slate-50 border-r border-slate-200 font-mono">/</span>
                        <input type="text" name="slug" x-model="slug" maxlength="191"
                               :placeholder="titulo ? titulo.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g,'').replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'') : 'mi-pagina'"
                               class="flex-1 px-3 py-2.5 text-sm font-mono outline-none">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">Sólo minúsculas, números y guiones.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Descripción para buscadores</label>
                    <textarea name="meta_descripcion" rows="3" maxlength="300"
                              class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-[#1a56c4] outline-none"
                              placeholder="Resumen de una o dos líneas que aparece en Google.">{{ old('meta_descripcion', $page->meta_descripcion) }}</textarea>
                </div>

                <label class="flex items-start gap-2.5 cursor-pointer pt-1">
                    <input type="checkbox" name="activo" value="1" class="mt-0.5 rounded border-slate-300"
                           {{ old('activo', $page->activo) ? 'checked' : '' }}>
                    <span>
                        <span class="block text-sm font-semibold text-slate-700">Publicada</span>
                        <span class="block text-[11px] text-slate-400 mt-0.5">Si la desmarcas queda como borrador y el sitio devuelve 404.</span>
                    </span>
                </label>
            </div>

            @if($editando)
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-xs text-slate-500 space-y-1">
                <p>Creada: {{ $page->created_at->isoFormat('D MMM YYYY, HH:mm') }}</p>
                <p>Última edición: {{ $page->updated_at->isoFormat('D MMM YYYY, HH:mm') }}</p>
            </div>
            @endif
        </div>
    </div>
</form>

@endsection
