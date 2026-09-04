@extends('layouts.app')
@section('title', 'Ingresar a tu Cuenta')

@section('content')

<div class="max-w-4xl mx-auto px-4 py-16">
    <div class="grid md:grid-cols-2 gap-8 items-start">

        {{-- ── LOGIN ───────────────────────────────────────────────────────── --}}
        <div class="card p-8">
            <div class="text-center mb-6">
                <div class="w-14 h-14 rounded-2xl bg-[#0A3D7A] flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h1 class="text-2xl font-black text-[#0A3D7A]" style="font-family:'Poppins',sans-serif;">Bienvenido de vuelta</h1>
                <p class="text-sm text-gray-500 mt-1">Ingresa a tu cuenta para ver tus pedidos</p>
            </div>

            @if($errors->has('email') || $errors->has('password'))
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm mb-5 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $errors->first('email') ?: $errors->first('password') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        placeholder="tu@email.cl" class="form-input" autofocus>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Contraseña</label>
                    <input type="password" name="password" required placeholder="••••••••" class="form-input">
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-500 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300">
                        Recordarme
                    </label>
                    <a href="#" class="text-sm text-[#1a56c4] hover:text-[#0A3D7A] transition">¿Olvidaste tu contraseña?</a>
                </div>
                <button type="submit" class="btn-primary w-full justify-center text-base py-3.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Ingresar
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-gray-100 text-center">
                <p class="text-sm text-gray-500 mb-3">O ingresa con</p>
                <a href="https://wa.me/56981493272?text=Hola!%20Quiero%20hacer%20un%20pedido%20sin%20registrarme" target="_blank"
                   class="btn-whatsapp w-full justify-center text-sm py-3">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Pedir sin cuenta — WhatsApp
                </a>
            </div>
        </div>

        {{-- ── REGISTER ─────────────────────────────────────────────────────── --}}
        <div class="card p-8">
            <div class="text-center mb-6">
                <div class="w-14 h-14 rounded-2xl bg-[#1FA855] flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <h2 class="text-2xl font-black text-[#0A3D7A]" style="font-family:'Poppins',sans-serif;">Crear cuenta gratis</h2>
                <p class="text-sm text-gray-500 mt-1">Registra tus pedidos y accede a promociones</p>
            </div>

            @if($errors->has('register_email') || $errors->has('nombre'))
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm mb-5">
                {{ $errors->first('register_email') ?: $errors->first('nombre') }}
            </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Nombre completo</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required
                        placeholder="María González" class="form-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Email</label>
                    <input type="email" name="register_email" value="{{ old('register_email') }}" required
                        placeholder="maria@ejemplo.cl" class="form-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Teléfono</label>
                    <input type="tel" name="telefono" value="{{ old('telefono') }}"
                        placeholder="+56 9 1234 5678" class="form-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Contraseña</label>
                    <input type="password" name="password" required placeholder="Mínimo 8 caracteres" class="form-input">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" required placeholder="Repite tu contraseña" class="form-input">
                </div>
                <label class="flex items-start gap-2 text-xs text-gray-500 cursor-pointer">
                    <input type="checkbox" required class="mt-0.5 rounded border-gray-300 text-[#1a56c4]">
                    <span>Acepto los <a href="#" class="text-[#1a56c4] underline">Términos y Condiciones</a> y la <a href="#" class="text-[#1a56c4] underline">Política de Privacidad</a></span>
                </label>
                <button type="submit" class="btn-primary w-full justify-center text-base py-3.5" style="background:#1FA855;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Crear mi cuenta
                </button>
            </form>

            {{-- Benefits --}}
            <div class="mt-6 pt-5 border-t border-gray-100">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Beneficios de registrarte</p>
                <ul class="space-y-1.5">
                    @foreach(['✓ Historial de pedidos siempre disponible','✓ Seguimiento en tiempo real','✓ Descuentos exclusivos para clientes','✓ Checkout más rápido'] as $b)
                    <li class="text-sm text-gray-600">{{ $b }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
