@extends('layouts.admin')
@section('title', 'Productos')
@section('page-title', 'Productos')
@section('breadcrumb', 'Gestión del catálogo')

@section('content')

{{-- Toolbar --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <form method="GET" class="flex gap-2 flex-wrap">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar producto..."
               class="px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none focus:border-[#1a56c4] w-52">
        <select name="categoria" class="px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none">
            <option value="">Todas las categorías</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @selected(request('categoria') == $cat->id)>{{ $cat->nombre }}</option>
            @endforeach
        </select>
        <select name="estado" class="px-3 py-2 text-sm border border-slate-200 rounded-lg outline-none">
            <option value="">Todos</option>
            <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
            <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivos</option>
        </select>
        <button type="submit" class="px-4 py-2 text-sm font-semibold bg-slate-800 text-white rounded-lg hover:bg-slate-700">Filtrar</button>
        @if(request()->hasAny(['q','categoria','estado']))
        <a href="{{ route('admin.productos.index') }}" class="px-4 py-2 text-sm font-semibold border border-slate-200 rounded-lg hover:bg-slate-50">Limpiar</a>
        @endif
    </form>

    <a href="{{ route('admin.productos.create') }}"
       class="shrink-0 flex items-center gap-2 px-4 py-2 bg-[#1a56c4] text-white text-sm font-semibold rounded-lg hover:bg-[#0A3D7A] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Nuevo producto
    </a>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Producto</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase hidden md:table-cell">Categoría</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Precio</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Stock</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase hidden lg:table-cell">Estado</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($productos as $producto)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-3">
                        <div>
                            <p class="font-semibold text-slate-800">{{ $producto->nombre }}</p>
                            <p class="text-xs text-slate-400 font-mono">{{ $producto->sku }}</p>
                        </div>
                    </td>
                    <td class="px-5 py-3 hidden md:table-cell">
                        <span class="inline-flex px-2 py-0.5 bg-slate-100 text-slate-600 text-xs font-medium rounded-full">
                            {{ $producto->category->nombre ?? '—' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <p class="font-semibold text-slate-800">${{ number_format($producto->precio, 0, ',', '.') }}</p>
                        @if($producto->tieneDescuento())
                        <p class="text-xs text-slate-400 line-through">${{ number_format($producto->precio_original, 0, ',', '.') }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right">
                        <span class="font-semibold {{ $producto->stock == 0 ? 'text-red-600' : ($producto->stockBajo() ? 'text-amber-600' : 'text-green-600') }}">
                            {{ $producto->stock }}
                        </span>
                    </td>
                    <td class="px-5 py-3 hidden lg:table-cell">
                        @if($producto->activo)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Activo
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-slate-100 text-slate-500 text-xs font-semibold rounded-full">
                            <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span> Inactivo
                        </span>
                        @endif
                        @if($producto->destacado)
                        <span class="ml-1 inline-flex px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">★ Dest.</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.productos.edit', $producto) }}"
                               class="px-3 py-1.5 text-xs font-semibold border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                                Editar
                            </a>
                            <form method="POST" action="{{ route('admin.productos.destroy', $producto) }}"
                                  onsubmit="return confirm('¿Desactivar este producto?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                                    Desactivar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-400">
                        No se encontraron productos.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($productos->hasPages())
    <div class="px-5 py-4 border-t border-slate-100">
        {{ $productos->links() }}
    </div>
    @endif
</div>

@endsection
