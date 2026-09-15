{{--
    Selector de destino. El tipo decide qué opciones se ofrecen, de modo que un
    enlace sólo pueda apuntar a algo que existe: una ruta de la lista blanca, una
    categoría del catálogo o una página publicada. "Dirección externa" es el único
    campo libre.
--}}
<div x-data="{ tipo: @js(old('tipo', $item->tipo ?? 'ruta')) }">
    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Apunta a *</label>
    <div class="flex gap-2">
        <select name="tipo" x-model="tipo"
                class="w-36 shrink-0 px-2 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#1a56c4] outline-none bg-white">
            <option value="ruta">Sección</option>
            <option value="categoria">Categoría</option>
            <option value="pagina">Página</option>
            <option value="url">Dirección</option>
        </select>

        {{-- Sección de la tienda --}}
        <template x-if="tipo === 'ruta'">
            <select name="destino" class="flex-1 min-w-0 px-2 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#1a56c4] outline-none bg-white">
                @foreach($rutas as $valor => $etiqueta)
                <option value="{{ $valor }}" {{ ($item->tipo ?? null) === 'ruta' && ($item->destino ?? null) === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                @endforeach
            </select>
        </template>

        {{-- Categoría del catálogo --}}
        <template x-if="tipo === 'categoria'">
            <select name="destino" class="flex-1 min-w-0 px-2 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#1a56c4] outline-none bg-white">
                @forelse($categorias as $categoria)
                <option value="{{ $categoria->slug }}" {{ ($item->tipo ?? null) === 'categoria' && ($item->destino ?? null) === $categoria->slug ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                @empty
                <option value="">No hay categorías activas</option>
                @endforelse
            </select>
        </template>

        {{-- Página editable --}}
        <template x-if="tipo === 'pagina'">
            <select name="destino" class="flex-1 min-w-0 px-2 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#1a56c4] outline-none bg-white">
                @forelse($paginas as $pagina)
                <option value="{{ $pagina->slug }}" {{ ($item->tipo ?? null) === 'pagina' && ($item->destino ?? null) === $pagina->slug ? 'selected' : '' }}>{{ $pagina->titulo }}</option>
                @empty
                <option value="">Todavía no hay páginas creadas</option>
                @endforelse
            </select>
        </template>

        {{-- Dirección libre --}}
        <template x-if="tipo === 'url'">
            <input type="text" name="destino" placeholder="https://ejemplo.cl  o  /ruta-interna"
                   value="{{ ($item->tipo ?? null) === 'url' ? ($item->destino ?? '') : '' }}"
                   class="flex-1 min-w-0 px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#1a56c4] outline-none">
        </template>
    </div>
</div>
