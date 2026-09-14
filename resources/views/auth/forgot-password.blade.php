@extends('layouts.app')
@section('title', 'Recuperar contraseña')

@section('content')

<div class="max-w-md mx-auto px-4 py-16">
    <div class="card p-8">
        <div class="text-center mb-6">
            <div class="w-14 h-14 rounded-2xl bg-[#0A3D7A] flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l6.964-6.964A6 6 0 1121 9z"/></svg>
            </div>
            <h1 class="text-2xl font-black text-[#0A3D7A]" style="font-family:'Poppins',sans-serif;">¿Olvidaste tu contraseña?</h1>
            <p class="text-sm text-gray-500 mt-1">Escribe tu correo y te enviamos un enlace para crear una nueva.</p>
        </div>

        @if(session('status'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm mb-5 flex items-start gap-2">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('status') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm mb-5 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       placeholder="tu@email.cl" class="form-input">
            </div>
            <button type="submit" class="btn-primary w-full justify-center text-base py-3.5">
                Enviarme el enlace
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-gray-100 text-center">
            <a href="{{ route('login') }}" class="text-sm font-semibold text-[#1a56c4] hover:text-[#0A3D7A] transition inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Volver a ingresar
            </a>
        </div>
    </div>
</div>
@endsection
