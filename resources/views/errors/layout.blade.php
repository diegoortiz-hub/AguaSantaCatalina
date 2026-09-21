{{--
    Página de error deliberadamente autónoma.

    No extiende layouts.app a propósito: ese layout arma el menú desde la base
    de datos, y si el error que estamos mostrando ES un problema de base de
    datos, la página de error reventaría también. Aquí no se consulta nada.
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <meta name="robots" content="noindex">
    <title>@yield('titulo') — Aguas Santa Catalina</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: radial-gradient(900px 420px at 50% -10%, #1a56c4 0%, transparent 62%), #08264E;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            color: #1e293b;
        }
        .tarjeta {
            width: 100%;
            max-width: 440px;
            background: #fff;
            border-radius: 18px;
            padding: 36px 32px;
            text-align: center;
            box-shadow: 0 24px 60px rgba(8, 38, 78, .35);
        }
        .logo { height: 44px; width: auto; margin-bottom: 20px; }
        .codigo {
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #1a56c4;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            border-radius: 999px;
            padding: 5px 12px;
            margin-bottom: 14px;
        }
        h1 { font-size: 21px; margin: 0 0 10px; color: #0f172a; }
        p { font-size: 15px; line-height: 1.6; color: #64748b; margin: 0 0 24px; }
        .acciones { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; }
        a.boton {
            display: inline-block;
            padding: 12px 22px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color .15s;
        }
        a.principal { background: #0A3D7A; color: #fff; }
        a.principal:hover { background: #1a56c4; }
        a.secundario { border: 1px solid #e2e8f0; color: #475569; }
        a.secundario:hover { background: #f8fafc; }
    </style>
</head>
<body>
    <main class="tarjeta">
        <img class="logo" src="{{ asset('images/logo.png') }}" alt="Aguas Santa Catalina">
        <span class="codigo">@yield('codigo')</span>
        <h1>@yield('titulo')</h1>
        <p>@yield('mensaje')</p>
        <div class="acciones">
            <a class="boton principal" href="{{ url('/') }}">Volver al inicio</a>
            <a class="boton secundario" href="{{ url('/contacto') }}">Contacto</a>
        </div>
    </main>
</body>
</html>
