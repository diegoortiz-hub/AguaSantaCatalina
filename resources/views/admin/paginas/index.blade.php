@extends('layouts.admin')
@section('title', 'Páginas')
@section('page-title', 'Páginas')

@section('content')

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800" style="font-family:'Poppins',sans-serif;">Páginas del sitio</h1>
        <p class="text-sm text-slate-400 mt-0.5">Contenido que se edita sin tocar código. Las direcciones quedan como <code class="text-xs bg-slate-100 px-1.5 py-0.5 rounded">/mi-pagina</code>.</p>
    </div>
    <a href="{{ route('admin.paginas.create') }}"
       class="flex items-center gap-2 px-4 py-2.5 bg-[#0A3D7A] text-white text-sm font-semibold rounded-xl hover:bg-[#1a56c4] transition-colors shadow-md">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nueva página
    </a>
</div>

@if(session('success'))
<div class="mb-5 flex items-start gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
    <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <span>{{ session('success') }}</span>
</div>
@endif

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm" style="min-width:640px">
            <thead>
                <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 text-[11px] font-mono uppercase tracking-wider font-semibold">
                    <th class="p-3.5">Página</th>
                    <th class="p-3.5">Dirección</th>
                    <th class="p-3.5">Estado</th>
                    <th class="p-3.5">Actualizada</th>
                    <th class="p-3.5 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($paginas as $pagina)
                <tr class="hover:bg-slate-50/60 transition-colors">
                    <td class="p-3.5">
                        <p class="font-semibold text-slate-800">{{ $pagina->titulo }}</p>
                        @if($pagina->bajada)
                        <p class="text-xs text-slate-400 mt-0.5">{{ Str::limit($pagina->bajada, 70) }}</p>
                        @endif
                    </td>
                    <td class="p-3.5">
                        <a href="{{ url('/'.$pagina->slug) }}" target="_blank" rel="noopener"
                           class="font-mono text-xs text-[#1a56c4] hover:underline inline-flex items-center gap-1">
                            /{{ $pagina->slug }}
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </td>
                    <td class="p-3.5">
                        @if($pagina->activo)
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-green-100 text-green-700">Publicada</span>
                        @else
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-200 text-slate-600">Borrador</span>
                        @endif
                    </td>
                    <td class="p-3.5 text-xs text-slate-400">{{ $pagina->updated_at->isoFormat('D MMM YYYY') }}</td>
                    <td class="p-3.5">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.paginas.edit', $pagina) }}"
                               class="px-3 py-1.5 text-xs font-semibold border border-slate-200 rounded-lg hover:border-[#0A3D7A] hover:text-[#0A3D7A] transition">Editar</a>
                            <form method="POST" action="{{ route('admin.paginas.destroy', $pagina) }}"
                                  onsubmit="return confirm('¿Eliminar «{{ $pagina->titulo }}»? Los enlaces del menú que apunten a ella dejarán de mostrarse.')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 text-xs font-semibold text-rose-600 border border-rose-200 rounded-lg hover:bg-rose-50 transition">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-10 text-center">
                        <p class="text-sm font-semibold text-slate-500">Todavía no hay páginas</p>
                        <p class="text-xs text-slate-400 mt-1">Crea la primera para publicar términos, privacidad o cualquier contenido.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 text-xs text-slate-500 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
    Para que una página aparezca en el sitio, agrégala como enlace en
    <a href="{{ route('admin.menus.index') }}" class="font-semibold text-[#1a56c4] hover:underline">Menús</a>.
    Una página publicada siempre es accesible por su dirección, aunque no esté en ningún menú.
</div>

@endsection
