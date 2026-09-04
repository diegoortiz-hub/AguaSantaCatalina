@extends('layouts.app')

@section('title', 'Catálogo de Productos')

@section('content')
<h1 style="margin-bottom:1.5rem;">Catálogo de Productos</h1>

<form method="GET" style="display:flex; gap:1rem; flex-wrap:wrap; margin-bottom:2rem; background:#f5f5f5; padding:1rem; border-radius:8px;">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar productos..." style="flex:1; min-width:200px; padding:.5rem 1rem; border:1px solid #ddd; border-radius:6px;">
    <select name="categoria" style="padding:.5rem 1rem; border:1px solid #ddd; border-radius:6px;">
        <option value="">Todas las categorías</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->slug }}" {{ request('categoria') === $cat->slug ? 'selected' : '' }}>{{ $cat->nombre }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-primary">Filtrar</button>
    <a href="{{ route('productos.index') }}" class="btn" style="background:#eee; color:#333;">Limpiar</a>
</form>

<div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(250px,1fr)); gap:1.5rem;">
    @forelse($products as $product)
    <div style="border:1px solid #eee; border-radius:10px; padding:1.2rem; position:relative;">
        @if($product->badge)
            <span class="badge badge-{{ $product->badge_color }}">{{ $product->badge }}</span>
        @endif
        <div style="height:120px; background:#e9f5ff; border-radius:8px; margin:1rem 0; display:flex; align-items:center; justify-content:center; font-size:3rem;">💧</div>
        <p style="font-size:.8rem; color:#0077b6; margin-bottom:.3rem;">{{ $product->category->nombre }}</p>
        <h3 style="font-size:1rem; margin-bottom:.5rem;">{{ $product->nombre }}</h3>
        <p style="color:#0077b6; font-size:1.3rem; font-weight:700;">${{ number_format($product->precio, 0, ',', '.') }}</p>
        @if($product->precio_original)
            <p style="color:#999; text-decoration:line-through; font-size:.85rem;">${{ number_format($product->precio_original, 0, ',', '.') }}</p>
        @endif
        <p style="font-size:.85rem; color:{{ $product->stock <= $product->stock_minimo ? '#d62828' : '#555' }}; margin-top:.3rem;">
            Stock: {{ $product->stock }} ud.
        </p>
        <a href="{{ route('productos.show', $product->slug) }}" class="btn btn-primary" style="margin-top:.8rem; display:block; text-align:center;">Ver detalles</a>
    </div>
    @empty
    <p style="grid-column:1/-1; text-align:center; color:#888; padding:2rem;">No se encontraron productos.</p>
    @endforelse
</div>

<div style="margin-top:2rem;">
    {{ $products->links() }}
</div>
@endsection
