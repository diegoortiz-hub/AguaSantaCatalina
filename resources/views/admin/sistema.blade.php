@extends('layouts.admin')
@section('title', 'Sistema')
@section('page-title', 'Sistema')

@section('content')

@php
    use App\Support\EstadoSistema as Es;

    $colores = [
        Es::OK    => ['punto' => 'bg-emerald-500', 'texto' => 'text-emerald-700', 'fondo' => 'bg-emerald-50', 'borde' => 'border-emerald-200', 'label' => 'Todo en orden'],
        Es::AVISO => ['punto' => 'bg-amber-500',   'texto' => 'text-amber-700',   'fondo' => 'bg-amber-50',   'borde' => 'border-amber-200',   'label' => 'Requiere atención'],
        Es::FALLA => ['punto' => 'bg-rose-500',    'texto' => 'text-rose-700',    'fondo' => 'bg-rose-50',    'borde' => 'border-rose-200',    'label' => 'Hay fallas'],
    ];

    $etiquetas = [
        'base_datos'     => 'Base de datos',
        'almacenamiento' => 'Almacenamiento',
        'cache'          => 'Caché',
        'cola'           => 'Cola de trabajos',
        'correo'         => 'Correo saliente',
        'migraciones'    => 'Migraciones',
        'entorno'        => 'Entorno',
        'version'        => 'Versión desplegada',
    ];

    $r = $colores[$resumen];

    // Polilínea de la serie de visitas.
    $puntos = '';
    if (count($serie) > 1) {
        $max  = max($serie) ?: 1;
        $paso = 800 / (count($serie) - 1);
        $puntos = collect($serie)->map(fn ($v, $i) => round($i * $paso, 1).','.round(140 - ($v / $max) * 120, 1))->implode(' ');
    }
@endphp

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800" style="font-family:'Poppins',sans-serif;">Sistema</h1>
        <p class="text-sm text-slate-400 mt-0.5">Salud del servicio y tráfico del sitio, medido en la propia aplicación.</p>
    </div>
    <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl border {{ $r['borde'] }} {{ $r['fondo'] }}">
        <span class="w-2 h-2 rounded-full {{ $r['punto'] }} {{ $resumen !== Es::OK ? 'animate-pulse' : '' }}"></span>
        <span class="text-xs font-bold {{ $r['texto'] }}">{{ $r['label'] }}</span>
    </div>
</div>

{{-- ── Salud del servicio ─────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-4">
    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/60">
        <h2 class="font-bold text-slate-800 text-base">Salud del servicio</h2>
        <p class="text-xs text-slate-400 mt-0.5">Comprobado ahora mismo, al cargar esta página.</p>
    </div>
    <div class="divide-y divide-slate-100">
        @foreach($comprobaciones as $clave => $c)
        @php $cc = $colores[$c['estado']]; @endphp
        <div class="px-5 py-3.5 flex flex-wrap items-center gap-3">
            <span class="w-2 h-2 rounded-full shrink-0 {{ $cc['punto'] }}"></span>
            <span class="text-sm font-semibold text-slate-800 w-44 shrink-0">{{ $etiquetas[$clave] ?? $clave }}</span>
            <span class="text-sm font-mono {{ $cc['texto'] }} shrink-0">{{ $c['valor'] }}</span>
            <span class="text-xs text-slate-400 flex-1 min-w-0">{{ $c['nota'] }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- ── Tráfico ────────────────────────────────────────────────────────── --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-3">
    <h2 class="font-bold text-slate-800 text-base">Tráfico del sitio</h2>
    <div class="flex gap-1 bg-slate-100 p-1 rounded-xl">
        @foreach([7 => '7 días', 30 => '30 días', 90 => '90 días'] as $d => $label)
        <a href="{{ route('admin.sistema', ['dias' => $d]) }}"
           class="px-3 py-1.5 text-xs rounded-lg transition-all {{ $dias === $d ? 'bg-white text-slate-800 shadow-sm font-semibold' : 'text-slate-500 hover:text-slate-700' }}">{{ $label }}</a>
        @endforeach
    </div>
</div>

@if(! $hayDatos)
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-10 text-center">
    <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-3">
        <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
    </div>
    <p class="text-sm font-semibold text-slate-500">Todavía no hay visitas registradas</p>
    <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">
        Se cuenta cada carga de página de la tienda. No se registran las visitas del equipo
        con sesión de administrador, ni las de rastreadores.
    </p>
</div>
@else

<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-4">
    @foreach([
        ['Visitas hoy',           number_format($visitasHoy, 0, ',', '.'),    'páginas cargadas'],
        ['Visitantes hoy',        number_format($visitantesHoy, 0, ',', '.'), 'personas distintas'],
        ['Visitas del período',   number_format($visitas, 0, ',', '.'),       "últimos {$dias} días"],
        ['Visitantes del período',number_format($visitantes, 0, ',', '.'),    "únicos por día, sumados"],
    ] as [$titulo, $valor, $nota])
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
        <p class="text-xs text-slate-400 font-medium uppercase tracking-wide">{{ $titulo }}</p>
        <p class="text-2xl font-bold text-slate-900 mt-1" style="font-variant-numeric:tabular-nums">{{ $valor }}</p>
        <p class="text-xs text-slate-400 mt-1">{{ $nota }}</p>
    </div>
    @endforeach
</div>

<div class="grid xl:grid-cols-5 gap-4 mb-4">

    {{-- Serie --}}
    <div class="xl:col-span-3 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 pt-5 pb-3 flex flex-wrap items-baseline justify-between gap-2 border-b border-slate-50">
            <div>
                <h3 class="font-bold text-slate-800 text-base">Visitas por día</h3>
                <p class="text-xs text-slate-400 mt-0.5">Máximo del período: {{ number_format(max($serie), 0, ',', '.') }}</p>
            </div>
            @if($conversion !== null)
            <div class="text-right">
                <p class="text-lg font-bold text-[#0A3D7A]" style="font-variant-numeric:tabular-nums">{{ number_format($conversion, 2, ',', '.') }}%</p>
                <p class="text-[11px] text-slate-400">pedidos por visitante</p>
            </div>
            @endif
        </div>
        <div class="px-5 py-4">
            @if($puntos)
            <svg viewBox="0 0 800 150" class="w-full" preserveAspectRatio="none" style="height:150px">
                <polyline fill="none" stroke="#1a56c4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" points="{{ $puntos }}"/>
            </svg>
            @else
            <p class="text-xs text-slate-400 py-10 text-center">Hacen falta al menos dos días con datos.</p>
            @endif
        </div>
    </div>

    {{-- Origen --}}
    <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <h3 class="font-bold text-slate-800 text-base mb-1">De dónde llegan</h3>
        <p class="text-xs text-slate-400 mb-4">Sitios que enviaron visitas</p>
        @forelse($origenesTop as $o)
        <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
            <span class="text-sm text-slate-600 truncate mr-2">{{ $o->origen }}</span>
            <span class="text-sm font-bold text-slate-800 shrink-0" style="font-variant-numeric:tabular-nums">{{ number_format($o->total, 0, ',', '.') }}</span>
        </div>
        @empty
        <p class="text-xs text-slate-400 py-6 text-center">
            Todas las visitas llegaron directo, sin pasar por otro sitio.
        </p>
        @endforelse
    </div>
</div>

{{-- Páginas más vistas --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/60">
        <h3 class="font-bold text-slate-800 text-base">Páginas más vistas</h3>
    </div>
    <div class="divide-y divide-slate-100">
        @php $tope = $paginasTop->max('total') ?: 1; @endphp
        @foreach($paginasTop as $p)
        <div class="px-5 py-3">
            <div class="flex items-center justify-between gap-3 mb-1.5">
                <a href="{{ url($p->ruta) }}" target="_blank" rel="noopener"
                   class="text-sm font-mono text-[#1a56c4] hover:underline truncate">{{ $p->ruta }}</a>
                <span class="text-sm font-bold text-slate-800 shrink-0" style="font-variant-numeric:tabular-nums">{{ number_format($p->total, 0, ',', '.') }}</span>
            </div>
            <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-[#1a56c4] rounded-full" style="width:{{ round(($p->total / $tope) * 100) }}%"></div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<div class="mt-4 text-xs text-slate-500 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 space-y-1">
    <p><strong class="text-slate-700">Sobre la medición:</strong> se cuenta en el servidor, después de enviar la respuesta, así que no agrega demora a ninguna página y no depende de que el visitante acepte scripts.</p>
    <p>No se guarda IP ni navegador. Para contar personas distintas se usa un código irreversible que cambia cada día, y se borra a los 90 días.</p>
</div>

@endsection
