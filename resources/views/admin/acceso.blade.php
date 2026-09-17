<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    {{-- La puerta del panel no tiene por qué estar en buscadores. --}}
    <meta name="robots" content="noindex, nofollow">
    <title>Acceso al panel — Santa Catalina</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen font-sans antialiased text-slate-800 flex items-center justify-center p-4"
      style="background:
          radial-gradient(1200px 500px at 50% -10%, #1a56c4 0%, transparent 60%),
          #08264E;">

    <main class="w-full max-w-sm">

        <div class="flex flex-col items-center mb-7">
            <img src="{{ asset('images/isotipo-blanco.png') }}" alt="" width="44" height="44" class="h-11 w-auto mb-3">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-sky-200/80">Panel de administración</p>
            <h1 class="text-xl font-bold text-white mt-1" style="font-family:'Poppins',sans-serif;">Aguas Santa Catalina</h1>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl border border-white/10 p-7">

            @if ($errors->any())
            <div class="mb-5 flex gap-2.5 rounded-xl bg-red-50 border border-red-100 px-3.5 py-3">
                <svg class="w-4 h-4 shrink-0 mt-0.5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <div class="text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}"
                           required autofocus autocomplete="username"
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400
                                  focus:border-[#1a56c4] focus:ring-2 focus:ring-[#1a56c4]/20 focus:outline-none transition"
                           placeholder="tu@aguassantacatalina.cl">
                </div>

                <div>
                    <label for="password" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Contraseña</label>
                    <input id="password" name="password" type="password"
                           required autocomplete="current-password"
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400
                                  focus:border-[#1a56c4] focus:ring-2 focus:ring-[#1a56c4]/20 focus:outline-none transition"
                           placeholder="••••••••">
                </div>

                <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2 pt-1">
                    <label class="flex items-center gap-2 text-sm text-slate-600 select-none">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))
                               class="rounded border-slate-300 text-[#1a56c4] focus:ring-[#1a56c4]/30">
                        Recordarme
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-[#1a56c4] hover:underline">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 rounded-xl bg-[#0A3D7A] hover:bg-[#08316b] text-white text-sm font-semibold px-4 py-3 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Entrar al panel
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-white/50 mt-6">
            ¿Eres cliente?
            <a href="{{ route('login') }}" class="text-sky-200 hover:text-white underline underline-offset-2">Inicia sesión en la tienda</a>
        </p>
    </main>
</body>
</html>
