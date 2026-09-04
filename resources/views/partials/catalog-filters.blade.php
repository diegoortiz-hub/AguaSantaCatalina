{{--
    Partial: catalog-filters
    Variables esperadas desde x-data del padre: minPrice, maxPrice, maxPriceInDb
--}}
<form method="GET" action="{{ route('productos.index') }}" id="filter-form"
      x-data="{
          minPrice: {{ (int) request('precio_min', 0) }},
          maxPrice: {{ (int) request('precio_max', $maxPriceInDb) }},
          maxDb: {{ $maxPriceInDb }},
          clampMin() {
              this.minPrice = Math.min(this.minPrice, this.maxPrice - 1000);
              if (this.minPrice < 0) this.minPrice = 0;
          },
          clampMax() {
              this.maxPrice = Math.max(this.maxPrice, this.minPrice + 1000);
              if (this.maxPrice > this.maxDb) this.maxPrice = this.maxDb;
          },
          minPct() { return this.maxDb > 0 ? Math.round((this.minPrice / this.maxDb) * 100) : 0; },
          maxPct() { return this.maxDb > 0 ? Math.round((this.maxPrice / this.maxDb) * 100) : 100; }
      }">

    {{-- preserve sort & search --}}
    @if(request('sort'))
    <input type="hidden" name="sort" value="{{ request('sort') }}">
    @endif
    @if(request('q'))
    <input type="hidden" name="q" value="{{ request('q') }}">
    @endif

    {{-- ── Categorías ──────────────────────────────────────────────────────── --}}
    <div class="card p-4 mb-4">
        <h3 class="font-semibold text-sm text-[#0A3D7A] mb-3 uppercase tracking-wide" style="font-family:'Poppins',sans-serif;">Categorías</h3>
        <ul class="space-y-1">
            <li>
                <a href="{{ route('productos.index', request()->except('categoria')) }}"
                   class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition {{ !request('categoria') ? 'bg-[#0A3D7A] text-white font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span>Todas</span>
                </a>
            </li>
            @foreach($categories as $cat)
            <li>
                <a href="{{ route('productos.index', array_merge(request()->except('categoria'), ['categoria' => $cat->slug])) }}"
                   class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition {{ request('categoria') === $cat->slug ? 'bg-[#0A3D7A] text-white font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                    <span>{{ $cat->nombre }}</span>
                    <span class="text-xs opacity-60">{{ $cat->products_count }}</span>
                </a>
            </li>
            @endforeach
        </ul>
    </div>

    {{-- ── Rango de Precio con slider ──────────────────────────────────────── --}}
    <div class="card p-4 mb-4">
        <h3 class="font-semibold text-sm text-[#0A3D7A] mb-3 uppercase tracking-wide" style="font-family:'Poppins',sans-serif;">Precio</h3>

        {{-- Track visual --}}
        <div class="relative h-2 bg-slate-200 rounded-full mb-5 mt-3">
            <div class="absolute h-2 rounded-full bg-[#0A3D7A]"
                 :style="`left:${minPct()}%;right:${100 - maxPct()}%`"></div>
            {{-- Min thumb --}}
            <input type="range" min="0" :max="maxDb" step="500"
                   x-model.number="minPrice" @input="clampMin()"
                   class="absolute inset-0 w-full h-2 opacity-0 cursor-pointer"
                   style="pointer-events:auto;">
            {{-- Max thumb --}}
            <input type="range" min="0" :max="maxDb" step="500"
                   x-model.number="maxPrice" @input="clampMax()"
                   class="absolute inset-0 w-full h-2 opacity-0 cursor-pointer"
                   style="pointer-events:auto;">
        </div>

        {{-- Hidden fields for form submit --}}
        <input type="hidden" name="precio_min" :value="minPrice">
        <input type="hidden" name="precio_max" :value="maxPrice">

        {{-- Labels --}}
        <div class="flex justify-between text-xs text-slate-600 font-semibold">
            <span>$<span x-text="minPrice.toLocaleString('es-CL')"></span></span>
            <span>$<span x-text="maxPrice.toLocaleString('es-CL')"></span></span>
        </div>

        {{-- Quick presets --}}
        <div class="flex flex-wrap gap-1.5 mt-3">
            @foreach([
                ['label' => 'Hasta $5.000',  'min' => 0,     'max' => 5000],
                ['label' => '$5k–$20k',       'min' => 5000,  'max' => 20000],
                ['label' => '+$20.000',        'min' => 20000, 'max' => 999999],
            ] as $preset)
            <button type="button"
                    @click="minPrice = {{ $preset['min'] }}; maxPrice = Math.min({{ $preset['max'] }}, maxDb)"
                    class="px-2 py-1 rounded-full text-[10px] font-semibold border border-slate-200 hover:border-[#0A3D7A] hover:text-[#0A3D7A] transition">
                {{ $preset['label'] }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- ── Disponibilidad ──────────────────────────────────────────────────── --}}
    <div class="card p-4 mb-4">
        <h3 class="font-semibold text-sm text-[#0A3D7A] mb-3 uppercase tracking-wide" style="font-family:'Poppins',sans-serif;">Disponibilidad</h3>
        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
            <input type="checkbox" name="solo_stock" value="1" {{ request('solo_stock') ? 'checked' : '' }}
                   class="rounded border-gray-300 text-[#1a56c4]">
            Solo con stock disponible
        </label>
        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer mt-2">
            <input type="checkbox" name="destacado" value="1" {{ request('destacado') ? 'checked' : '' }}
                   class="rounded border-gray-300 text-[#1a56c4]">
            Solo productos destacados
        </label>
    </div>

    {{-- Botones --}}
    <button type="submit" class="btn-primary w-full justify-center">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
        APLICAR FILTROS
    </button>
    @if(request()->hasAny(['precio_min','precio_max','solo_stock','destacado']))
    <a href="{{ route('productos.index', request()->only(['q','categoria','sort'])) }}"
       class="block text-center text-xs text-gray-400 hover:text-gray-600 mt-2 transition">
        Limpiar filtros de precio/stock
    </a>
    @endif
</form>
