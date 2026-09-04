@php use Illuminate\Support\Facades\Storage; @endphp
@extends('layouts.admin')
@section('title', 'Banners')
@section('page-title', 'Banners')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800" style="font-family:'Poppins',sans-serif;">Banners y Promociones</h1>
        <p class="text-xs text-slate-500 mt-0.5">Administra los banners publicitarios y mensajes destacados de la tienda</p>
    </div>
    <button onclick="document.getElementById('modal-banner').classList.remove('hidden')"
            class="flex items-center gap-2 bg-[#1a56c4] hover:bg-[#0A3D7A] text-white px-4 py-2.5 text-sm font-bold rounded-xl shadow-md shadow-blue-500/20 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nuevo Banner
    </button>
</div>

{{-- Grid de banners --}}
@if($banners->isEmpty())
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-14 text-center">
    <svg class="w-12 h-12 text-slate-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
    <p class="text-slate-500 text-sm font-medium mb-1">Sin banners todavía</p>
    <p class="text-slate-400 text-xs">Crea el primer banner haciendo clic en "Nuevo Banner".</p>
</div>
@else
<div class="grid md:grid-cols-2 gap-6">
    @foreach($banners as $banner)
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col group hover:shadow-md transition-all">
        {{-- Preview --}}
        <div class="relative h-44 w-full bg-slate-900 overflow-hidden">
            @if($banner->imagen)
            <img src="{{ Str::startsWith($banner->imagen, 'http') ? $banner->imagen : Storage::url($banner->imagen) }}"
                 alt="{{ $banner->titulo }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80">
            @else
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#0A3D7A] to-[#1a56c4]">
                <svg class="w-12 h-12 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent flex flex-col justify-end p-4 text-white">
                <h3 class="font-bold text-base leading-tight text-white line-clamp-1">{{ $banner->titulo }}</h3>
                @if($banner->subtitulo)
                <p class="text-xs text-white/70 mt-0.5 line-clamp-1">{{ $banner->subtitulo }}</p>
                @endif
            </div>
            <div class="absolute top-3 right-3">
                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full shadow-md backdrop-blur-md flex items-center gap-1
                    {{ $banner->activo ? 'bg-emerald-500/90 text-white' : 'bg-slate-700/90 text-slate-300' }}">
                    <span class="w-2 h-2 rounded-full {{ $banner->activo ? 'bg-white animate-pulse' : 'bg-slate-400' }}"></span>
                    {{ $banner->activo ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
        </div>
        {{-- Detalles --}}
        <div class="p-5 flex-1 flex flex-col justify-between">
            <div class="space-y-1.5">
                @if($banner->link)
                <div class="flex items-center gap-2 text-xs text-slate-400">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span class="font-mono truncate text-slate-600">{{ $banner->link }}</span>
                </div>
                @endif
                <p class="text-xs text-slate-400">Orden: {{ $banner->orden }}</p>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                {{-- Toggle --}}
                <form method="POST" action="{{ route('admin.banners.toggle', $banner) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-xl border transition-all
                        {{ $banner->activo ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                        {{ $banner->activo ? 'Desactivar' : 'Activar' }}
                    </button>
                </form>
                {{-- Eliminar --}}
                <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}"
                      onsubmit="return confirm('¿Eliminar el banner «{{ addslashes($banner->titulo) }}»?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-1.5 rounded-xl text-rose-500 hover:bg-rose-50 transition-colors" title="Eliminar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Modal crear banner --}}
<div id="modal-banner" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-slate-800 text-lg">Nuevo Banner</h2>
            <button onclick="document.getElementById('modal-banner').classList.add('hidden')"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Título <span class="text-rose-500">*</span></label>
                <input type="text" name="titulo" required placeholder="ej. Hidratación sin límites"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm outline-none focus:border-[#1a56c4]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Subtítulo</label>
                <input type="text" name="subtitulo" placeholder="Descripción breve del banner"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm outline-none focus:border-[#1a56c4]">
            </div>

            {{-- Zona de subida de imagen --}}
            <div x-data="{ preview: null, dragover: false }">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                    Imagen del banner
                    <span class="ml-1 font-normal text-slate-400">(JPG, PNG, WebP · máx. 4 MB)</span>
                </label>
                <div @dragover.prevent="dragover=true" @dragleave="dragover=false"
                     @drop.prevent="dragover=false; const f=$event.dataTransfer.files[0]; if(f){const r=new FileReader(); r.onload=e=>preview=e.target.result; r.readAsDataURL(f); $refs.fileInput.files=$event.dataTransfer.files}"
                     :class="dragover ? 'border-[#1a56c4] bg-blue-50' : 'border-slate-200 bg-slate-50 hover:bg-slate-100'"
                     class="relative border-2 border-dashed rounded-xl transition-colors cursor-pointer overflow-hidden"
                     onclick="$refs.fileInput.click()">
                    {{-- Preview --}}
                    <template x-if="preview">
                        <img :src="preview" class="w-full h-36 object-cover">
                    </template>
                    <template x-if="!preview">
                        <div class="flex flex-col items-center justify-center py-8 gap-2">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-sm text-slate-500 font-medium">Arrastra una imagen o haz clic para seleccionar</p>
                            <p class="text-xs text-slate-400">Tamaño recomendado: <strong>1920 × 600 px</strong> (ratio 16:5)</p>
                        </div>
                    </template>
                    <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp" x-ref="fileInput"
                           @change="const f=$event.target.files[0]; if(f){const r=new FileReader(); r.onload=e=>preview=e.target.result; r.readAsDataURL(f)}"
                           class="hidden">
                </div>
                <template x-if="preview">
                    <button type="button" @click="preview=null; $refs.fileInput.value=''"
                            class="mt-1.5 text-xs text-rose-500 hover:text-rose-700 font-medium">Quitar imagen</button>
                </template>
                <p class="mt-1.5 text-[11px] text-slate-400">
                    La imagen se recortará automáticamente para adaptarse al banner. Formatos: JPG, PNG, WebP.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Link destino</label>
                    <input type="text" name="link" placeholder="/productos"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm outline-none focus:border-[#1a56c4]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Orden</label>
                    <input type="number" name="orden" value="0" min="0"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm outline-none focus:border-[#1a56c4]">
                </div>
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="activo" value="1" checked class="w-4 h-4 rounded text-[#1a56c4]">
                <span class="text-sm text-slate-700 font-medium">Activar inmediatamente</span>
            </label>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-banner').classList.add('hidden')"
                        class="flex-1 py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 py-2.5 bg-[#1a56c4] text-white text-sm font-semibold rounded-xl hover:bg-[#0A3D7A] transition-colors">
                    Subir Banner
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
