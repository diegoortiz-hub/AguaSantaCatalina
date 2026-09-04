@extends('layouts.app')

@section('title', 'Mi Cuenta')

@section('content')
<h1>Mi Cuenta</h1>
<p>Bienvenido/a, <strong>{{ auth()->user()->nombre }}</strong></p>
<p style="margin-top:1rem;"><a href="{{ route('home') }}" class="btn btn-primary">← Volver a la tienda</a></p>
@endsection
