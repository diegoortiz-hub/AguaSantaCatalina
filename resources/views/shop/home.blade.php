@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<section style="text-align:center; padding:3rem 0; background:linear-gradient(135deg,#023e8a,#0077b6); color:#fff; border-radius:12px; margin-bottom:2rem;">
    <h1 style="font-size:2.5rem; margin-bottom:1rem;">💧 Agua Purificada Premium</h1>
    <p style="font-size:1.2rem; opacity:.9; margin-bottom:2rem;">Calidad certificada, entrega a domicilio en Santiago</p>
    <a href="{{ route('productos.index') }}" class="btn btn-primary" style="font-size:1.1rem; padding:.8rem 2rem;">Ver productos</a>
    <a href="#" class="btn btn-whatsapp" style="font-size:1.1rem; padding:.8rem 2rem; margin-left:1rem;">💬 Pedir por WhatsApp</a>
</section>

<h2 style="margin-bottom:1.5rem;">Productos Destacados</h2>
<div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(250px,1fr)); gap:1.5rem;">
    @foreach($destacados as $product)
    <div style="border:1px solid #eee; border-radius:10px; padding:1.2rem; position:relative;">
        @if($product->badge)
            <span class="badge badge-{{ $product->badge_color }}">{{ $product->badge }}</span>
        @endif
        <div style="height:120px; background:#e9f5ff; border-radius:8px; margin:1rem 0; display:flex; align-items:center; justify-content:center; font-size:3rem;">💧</div>
        <h3 style="font-size:1rem; margin-bottom:.5rem;">{{ $product->nombre }}</h3>
        <p style="color:#0077b6; font-size:1.3rem; font-weight:700;">${{ number_format($product->precio, 0, ',', '.') }}</p>
        @if($product->precio_original)
            <p style="color:#999; text-decoration:line-through; font-size:.9rem;">${{ number_format($product->precio_original, 0, ',', '.') }}</p>
        @endif
        <a href="{{ route('productos.show', $product->slug) }}" class="btn btn-primary" style="margin-top:.8rem; display:block; text-align:center;">Ver detalles</a>
    </div>
    @endforeach
</div>

<section style="margin-top:3rem;">
    <h2 style="margin-bottom:1.5rem;">Nuestras Categorías</h2>
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:1rem;">
        @foreach($categories as $cat)
        <a href="{{ route('productos.index', ['categoria' => $cat->slug]) }}"
           style="display:block; padding:1.5rem; border:2px solid #0077b6; border-radius:10px; text-align:center; text-decoration:none; color:#0077b6; font-weight:600; transition:all .2s;">
            {{ $cat->nombre }}
        </a>
        @endforeach
    </div>
</section>
@endsection
