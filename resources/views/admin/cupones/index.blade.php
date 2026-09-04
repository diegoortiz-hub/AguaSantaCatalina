@extends('layouts.admin')
@section('title', 'Cupones')
@section('page-title', 'Cupones')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800" style="font-family:'Poppins',sans-serif;">Cupones de Descuento</h1>
        <p class="text-xs text-slate-500 mt-0.5">Crea códigos de descuento en porcentaje, monto fijo o despacho gratuito</p>
    </div>
    <button onclick="document.getElementById('modal-cupon').classList.remove('hidden')"
            class="flex items-center gap-2 bg-[#1a56c4] hover:bg-[#0A3D7A] text-white px-4 py-2.5 text-sm font-bold rounded-xl shadow-md shadow-blue-500/20 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Crear Cupón
    </button>
</div>

{{-- Búsqueda --}}
<form method="GET" class="mb-4">
    <div class="flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por código..."
               class="flex-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm outline-none focus:border-[#1a56c4] bg-white max-w-xs">
        <button type="submit" class="px-4 py-2.5 bg-[#0A3D7A] text-white text-sm font-semibold rounded-xl hover:bg-[#1a56c4] transition-colors">Buscar</button>
        @if(request('q'))
        <a href="{{ route('admin.cupones.index') }}" class="px-4 py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors">Limpiar</a>
        @endif
    </div>
</form>

{{-- Tabla --}}
<div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 text-[11px] font-mono uppercase tracking-wider font-semibold">
                    <th class="py-3.5 px-4">Código de Cupón</th>
                    <th class="py-3.5 px-4">Beneficio</th>
                    <th class="py-3.5 px-4 hidden md:table-cell">Compra Mínima</th>
                    <th class="py-3.5 px-4 text-center">Usos</th>
                    <th class="py-3.5 px-4 hidden lg:table-cell">Vencimiento</th>
                    <th class="py-3.5 px-4 text-center">Estado</th>
                    <th class="py-3.5 px-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100" x-data="{ copied: null }">
                @forelse($cupones as $cupon)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    {{-- Código --}}
                    <td class="py-3.5 px-4">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-extrabold text-sm text-[#1a56c4] bg-blue-50/80 px-2.5 py-1 rounded-lg border border-blue-200/60 tracking-wider uppercase">
                                {{ $cupon->codigo }}
                            </span>
                            <button x-data
                                    @click="navigator.clipboard.writeText('{{ $cupon->codigo }}'); $dispatch('notify', {msg: 'Código copiado'})"
                                    class="p-1 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors" title="Copiar">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                    </td>
                    {{-- Beneficio --}}
                    <td class="py-3.5 px-4">
                        @if($cupon->tipo === 'porcentaje')
                        <span class="font-bold text-purple-700 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M8 8h.01M16 16h.01"/></svg>
                            {{ number_format($cupon->descuento, 0) }}% OFF
                        </span>
                        @else
                        <span class="font-bold text-slate-800 font-mono">${{ number_format($cupon->descuento, 0, ',', '.') }} de Descuento</span>
                        @endif
                    </td>
                    {{-- Mínimo --}}
                    <td class="py-3.5 px-4 font-mono text-slate-600 font-medium hidden md:table-cell">
                        ${{ number_format($cupon->minimo_compra, 0, ',', '.') }}
                    </td>
                    {{-- Usos --}}
                    <td class="py-3.5 px-4 text-center">
                        <span class="font-mono font-bold bg-slate-100 text-slate-800 px-2.5 py-0.5 rounded-full text-xs">
                            {{ $cupon->usos_actuales }}{{ $cupon->maximo_usos ? ' / '.$cupon->maximo_usos : '' }}
                        </span>
                    </td>
                    {{-- Vencimiento --}}
                    <td class="py-3.5 px-4 text-slate-600 font-medium hidden lg:table-cell">
                        @if($cupon->vence_en)
                            @if($cupon->vence_en->isPast())
                            <span class="text-rose-500 font-semibold">Vencido {{ $cupon->vence_en->format('d/m/Y') }}</span>
                            @else
                            {{ $cupon->vence_en->format('d/m/Y') }}
                            @endif
                        @else
                        Sin límite
                        @endif
                    </td>
                    {{-- Estado --}}
                    <td class="py-3.5 px-4 text-center">
                        <form method="POST" action="{{ route('admin.cupones.toggle', $cupon) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold border transition-all
                                    {{ $cupon->activo ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $cupon->activo ? 'bg-emerald-600' : 'bg-slate-400' }}"></span>
                                {{ $cupon->activo ? 'Activo' : 'Pausado' }}
                            </button>
                        </form>
                    </td>
                    {{-- Acciones --}}
                    <td class="py-3.5 px-4 text-right">
                        <form method="POST" action="{{ route('admin.cupones.destroy', $cupon) }}"
                              onsubmit="return confirm('¿Eliminar el cupón {{ $cupon->codigo }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-xl text-rose-500 hover:bg-rose-50 transition-colors" title="Eliminar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-12 text-center text-slate-400">No se encontraron cupones registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($cupones->hasPages())
    <div class="px-5 py-4 border-t border-slate-100">{{ $cupones->links() }}</div>
    @endif
</div>

{{-- Modal crear cupón --}}
<div id="modal-cupon" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-slate-800 text-lg">Nuevo Cupón</h2>
            <button onclick="document.getElementById('modal-cupon').classList.add('hidden')"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.cupones.store') }}" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Código <span class="text-rose-500">*</span></label>
                    <input type="text" name="codigo" required placeholder="ej. VERANO20" style="text-transform:uppercase"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-mono uppercase outline-none focus:border-[#1a56c4]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tipo</label>
                    <select name="tipo" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm outline-none focus:border-[#1a56c4]">
                        <option value="porcentaje">Porcentaje (%)</option>
                        <option value="monto">Monto fijo ($)</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Descuento <span class="text-rose-500">*</span></label>
                    <input type="number" name="descuento" required min="0" step="0.01" placeholder="ej. 20"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm outline-none focus:border-[#1a56c4]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Compra mínima ($)</label>
                    <input type="number" name="minimo_compra" min="0" value="0" placeholder="0"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm outline-none focus:border-[#1a56c4]">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Máximo de usos</label>
                    <input type="number" name="maximo_usos" min="1" placeholder="Sin límite"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm outline-none focus:border-[#1a56c4]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Fecha de vencimiento</label>
                    <input type="date" name="vence_en"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm outline-none focus:border-[#1a56c4]">
                </div>
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="activo" value="1" checked class="w-4 h-4 rounded text-[#1a56c4]">
                <span class="text-sm text-slate-700 font-medium">Activar inmediatamente</span>
            </label>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-cupon').classList.add('hidden')"
                        class="flex-1 py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 py-2.5 bg-[#1a56c4] text-white text-sm font-semibold rounded-xl hover:bg-[#0A3D7A] transition-colors">
                    Crear Cupón
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
