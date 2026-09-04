@extends('layouts.admin')
@section('title', 'Clientes')
@section('page-title', 'Clientes')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800" style="font-family:'Poppins',sans-serif;">Base de Clientes</h1>
        <p class="text-xs text-slate-500 mt-0.5">Registro de compradores recurrentes y contactos de Aguas Santa Catalina</p>
    </div>
</div>

{{-- Métricas --}}
<div class="grid sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-xl bg-blue-100 text-[#1a56c4] flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div>
            <span class="text-xs text-slate-500 font-medium">Clientes Registrados</span>
            <p class="font-mono text-xl font-extrabold text-slate-900">{{ $total }}</p>
        </div>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
        </div>
        <div>
            <span class="text-xs text-slate-500 font-medium">Gasto Promedio por Cliente</span>
            <p class="font-mono text-xl font-extrabold text-emerald-700">${{ number_format($gasto_promedio, 0, ',', '.') }}</p>
        </div>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
        <div>
            <span class="text-xs text-slate-500 font-medium">Volumen Histórico Total</span>
            <p class="font-mono text-xl font-extrabold text-purple-700">${{ number_format($volumenTotal, 0, ',', '.') }}</p>
        </div>
    </div>
</div>

{{-- Búsqueda --}}
<form method="GET" class="mb-4">
    <div class="flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre o email..."
               class="flex-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm outline-none focus:border-[#1a56c4] bg-white">
        <button type="submit" class="px-4 py-2.5 bg-[#0A3D7A] text-white text-sm font-semibold rounded-xl hover:bg-[#1a56c4] transition-colors">Buscar</button>
        @if(request('q'))
        <a href="{{ route('admin.clientes.index') }}" class="px-4 py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors">Limpiar</a>
        @endif
    </div>
</form>

{{-- Tabla --}}
<div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 text-[11px] font-mono uppercase tracking-wider font-semibold">
                    <th class="py-3.5 px-4">Cliente</th>
                    <th class="py-3.5 px-4 hidden md:table-cell">Email</th>
                    <th class="py-3.5 px-4 text-right">Total Compras</th>
                    <th class="py-3.5 px-4 text-center">N° Pedidos</th>
                    <th class="py-3.5 px-4 hidden lg:table-cell">Registrado</th>
                    <th class="py-3.5 px-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100">
                @forelse($clientes as $cliente)
                @php
                    $initials = collect(explode(' ', $cliente->nombre ?? 'C'))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join('');
                    $colors = ['bg-violet-500','bg-blue-500','bg-emerald-500','bg-amber-500','bg-rose-500','bg-indigo-500','bg-teal-500'];
                    $color = $colors[crc32($cliente->nombre ?? '') % count($colors)];
                    $telLimpio = preg_replace('/[^0-9]/', '', $cliente->telefono ?? '');
                    $waUrl = $telLimpio ? 'https://api.whatsapp.com/send?phone=56'.$telLimpio.'&text=Hola%20'.urlencode($cliente->nombre ?? '').',%20te%20saludamos%20de%20Aguas%20Santa%20Catalina.' : null;
                @endphp
                <tr class="hover:bg-slate-50/80 transition-colors group">
                    <td class="py-3.5 px-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full {{ $color }} text-white font-bold flex items-center justify-center text-xs shrink-0">{{ $initials }}</div>
                            <div>
                                <p class="font-bold text-slate-900 text-sm">{{ $cliente->nombre }}</p>
                                <span class="text-[10px] text-slate-400">Desde {{ $cliente->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-3.5 px-4 hidden md:table-cell">
                        <p class="text-slate-700 font-medium">{{ $cliente->email }}</p>
                        @if($cliente->telefono)
                        <p class="text-slate-400 font-mono text-[11px]">{{ $cliente->telefono }}</p>
                        @endif
                    </td>
                    <td class="py-3.5 px-4 text-right font-mono font-extrabold text-slate-900 text-sm">
                        ${{ number_format($cliente->orders_sum_total ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <span class="font-mono font-bold bg-blue-50 text-[#1a56c4] px-2.5 py-0.5 rounded-full text-xs">{{ $cliente->orders_count ?? 0 }}</span>
                    </td>
                    <td class="py-3.5 px-4 text-slate-600 font-medium hidden lg:table-cell">{{ $cliente->created_at->format('d/m/Y') }}</td>
                    <td class="py-3.5 px-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            @if($waUrl)
                            <a href="{{ $waUrl }}" target="_blank"
                               class="p-2 rounded-xl text-emerald-600 hover:bg-emerald-50 transition-colors" title="WhatsApp directo">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </a>
                            @endif
                            <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-500 text-xs font-semibold">Ver Perfil</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-12 text-center text-slate-400">No se encontraron clientes con ese criterio.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clientes->hasPages())
    <div class="px-5 py-4 border-t border-slate-100">{{ $clientes->links() }}</div>
    @endif
</div>

@endsection
