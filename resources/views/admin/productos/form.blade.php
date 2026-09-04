@php use Illuminate\Support\Facades\Storage; @endphp
@extends('layouts.admin')
@section('title', $product->exists ? 'Editar Producto' : 'Nuevo Producto')
@section('page-title', $product->exists ? 'Editar Producto' : 'Nuevo Producto')
@section('breadcrumb', 'Productos / '.($product->exists ? $product->nombre : 'Crear'))

@section('content')

<form method="POST"
      action="{{ $product->exists ? route('admin.productos.update', $product) : route('admin.productos.store') }}"
      enctype="multipart/form-data"
      class="max-w-3xl space-y-6">
    @csrf
    @if($product->exists) @method('PUT') @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Información básica --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">Información básica</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">Nombre <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $product->nombre) }}" required
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm outline-none focus:border-[#1a56c4] focus:ring-1 focus:ring-[#1a56c4]/20">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">Categoría <span class="text-red-500">*</span></label>
                <select name="category_id" required class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm outline-none focus:border-[#1a56c4]">
                    <option value="">Seleccionar...</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id) == $cat->id)>{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">SKU</label>
                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm outline-none focus:border-[#1a56c4]">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">Descripción</label>
                <textarea name="descripcion" rows="4"
                          class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm outline-none focus:border-[#1a56c4] resize-none">{{ old('descripcion', $product->descripcion) }}</textarea>
            </div>
        </div>
    </div>

    {{-- Precios y stock --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">Precios y Stock</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">Precio <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">$</span>
                    <input type="number" name="precio" value="{{ old('precio', (int)$product->precio) }}" required min="0"
                           class="w-full pl-7 pr-3 py-2.5 border border-slate-200 rounded-lg text-sm outline-none focus:border-[#1a56c4]">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">Precio Original (tachado)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">$</span>
                    <input type="number" name="precio_original" value="{{ old('precio_original', (int)$product->precio_original) }}" min="0"
                           class="w-full pl-7 pr-3 py-2.5 border border-slate-200 rounded-lg text-sm outline-none focus:border-[#1a56c4]">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">Stock actual <span class="text-red-500">*</span></label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" required min="0"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm outline-none focus:border-[#1a56c4]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">Stock mínimo <span class="text-red-500">*</span></label>
                <input type="number" name="stock_minimo" value="{{ old('stock_minimo', $product->stock_minimo ?? 5) }}" required min="0"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm outline-none focus:border-[#1a56c4]">
            </div>
        </div>
    </div>

    {{-- Imagen del producto --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6"
         x-data="{
            preview: '{{ $product->imagen ? (Str::startsWith($product->imagen, "http") ? $product->imagen : Storage::url($product->imagen)) : "" }}',
            dragging: false,
            handleFile(file) {
                if (!file || !file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = e => { this.preview = e.target.result; };
                reader.readAsDataURL(file);
            }
         }">
        <h2 class="font-semibold text-slate-800 mb-1">Imagen del producto</h2>
        <p class="text-xs text-slate-400 mb-4">Recomendado: 800 × 800 px, formato cuadrado. JPG, PNG o WebP, máx. 4 MB.</p>

        <div class="grid sm:grid-cols-2 gap-6 items-start">
            {{-- Drop zone --}}
            <label
                class="relative flex flex-col items-center justify-center border-2 border-dashed rounded-xl cursor-pointer transition-all h-44"
                :class="dragging ? 'border-[#1a56c4] bg-blue-50' : 'border-slate-300 hover:border-[#1a56c4] hover:bg-slate-50'"
                @dragover.prevent="dragging = true"
                @dragleave.prevent="dragging = false"
                @drop.prevent="dragging = false; handleFile($event.dataTransfer.files[0]); $refs.imgInput.files = $event.dataTransfer.files"
            >
                <input type="file" name="imagen" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer" x-ref="imgInput"
                       @change="handleFile($event.target.files[0])">
                <svg class="w-8 h-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-xs font-semibold text-slate-500">Arrastra una imagen aquí</span>
                <span class="text-[11px] text-slate-400 mt-0.5">o haz clic para seleccionar</span>
            </label>

            {{-- Preview --}}
            <div class="h-44 rounded-xl border border-slate-200 overflow-hidden flex items-center justify-center bg-slate-50">
                <template x-if="preview">
                    <img :src="preview" class="w-full h-full object-contain" alt="Vista previa">
                </template>
                <template x-if="!preview">
                    <div class="text-center">
                        <svg class="w-10 h-10 text-slate-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-xs text-slate-400">Sin imagen aún</span>
                    </div>
                </template>
            </div>
        </div>

        @if($product->imagen)
        <p class="mt-3 text-xs text-slate-400">
            Imagen actual guardada. Selecciona una nueva para reemplazarla.
        </p>
        @endif
    </div>

    {{-- Badge y visibilidad --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">Etiqueta y Visibilidad</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">Etiqueta (badge)</label>
                <input type="text" name="badge" value="{{ old('badge', $product->badge) }}" placeholder="Ej: Más vendido"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm outline-none focus:border-[#1a56c4]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1.5">Color del badge</label>
                <select name="badge_color" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm outline-none">
                    <option value="">Sin color</option>
                    @foreach(['blue'=>'Azul','green'=>'Verde','red'=>'Rojo','orange'=>'Naranja','purple'=>'Morado'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('badge_color', $product->badge_color) === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex flex-wrap gap-6 mt-4">
            <label class="flex items-center gap-2.5 cursor-pointer">
                <input type="hidden" name="activo" value="0">
                <input type="checkbox" name="activo" value="1" @checked(old('activo', $product->activo ?? true))
                       class="w-4 h-4 rounded border-slate-300 text-[#1a56c4]">
                <span class="text-sm font-medium text-slate-700">Producto activo (visible en tienda)</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer">
                <input type="hidden" name="destacado" value="0">
                <input type="checkbox" name="destacado" value="1" @checked(old('destacado', $product->destacado))
                       class="w-4 h-4 rounded border-slate-300 text-[#1a56c4]">
                <span class="text-sm font-medium text-slate-700">Destacado en página de inicio</span>
            </label>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3">
        <button type="submit"
                class="px-6 py-2.5 bg-[#1a56c4] text-white text-sm font-semibold rounded-lg hover:bg-[#0A3D7A] transition-colors">
            {{ $product->exists ? 'Guardar cambios' : 'Crear producto' }}
        </button>
        <a href="{{ route('admin.productos.index') }}"
           class="px-6 py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-50 transition-colors">
            Cancelar
        </a>
    </div>
</form>

@endsection
