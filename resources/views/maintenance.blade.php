<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sitio en mantenimiento — Aguas Santa Catalina</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --blue-dark: #0A3D7A;
            --blue: #1E6FBF;
            --blue-light: #7dd3fc;
            --white: #ffffff;
        }
        body {
            min-height: 100dvh;
            background: linear-gradient(135deg, var(--blue-dark) 0%, var(--blue) 55%, #0e5099 100%);
            font-family: 'Inter', system-ui, sans-serif;
            color: var(--white);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }

        /* Decorative blobs */
        body::before {
            content: '';
            position: absolute;
            top: -120px; right: -120px;
            width: 480px; height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,.08) 0%, transparent 70%);
            pointer-events: none;
        }
        body::after {
            content: '';
            position: absolute;
            bottom: -80px; left: -80px;
            width: 360px; height: 360px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(125,211,252,.10) 0%, transparent 70%);
            pointer-events: none;
        }

        .card {
            position: relative;
            z-index: 1;
            max-width: 540px;
            width: 100%;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 24px;
            padding: 48px 40px;
            text-align: center;
            backdrop-filter: blur(12px);
        }

        .icon-wrap {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.2);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 28px;
            animation: float 3s ease-in-out infinite;
        }
        .icon-wrap svg {
            width: 38px; height: 38px;
            stroke: rgba(255,255,255,.85);
        }
        @keyframes float {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(-8px); }
        }

        .brand {
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(125,211,252,.85);
            margin-bottom: 12px;
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(26px, 5vw, 36px);
            font-weight: 900;
            line-height: 1.15;
            color: var(--white);
            margin-bottom: 16px;
        }

        .message {
            font-size: 15px;
            line-height: 1.65;
            color: rgba(255,255,255,.75);
            max-width: 400px;
            margin: 0 auto 32px;
        }

        .fin-box {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 999px;
            padding: 8px 20px;
            font-size: 13px;
            font-weight: 600;
            color: var(--blue-light);
            margin-bottom: 36px;
        }
        .fin-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #34d399;
            animation: pulse 1.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%,100% { opacity: 1; transform: scale(1); }
            50%      { opacity: .5; transform: scale(1.3); }
        }

        .divider {
            height: 1px;
            background: rgba(255,255,255,.12);
            margin-bottom: 28px;
        }

        .contact-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: rgba(255,255,255,.45);
            margin-bottom: 14px;
        }

        .contact-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }
        .contact-links a {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all .2s;
        }
        .btn-whatsapp {
            background: #25D366;
            color: #fff;
        }
        .btn-whatsapp:hover { background: #1ebe5a; }
        .btn-email {
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.2);
            color: rgba(255,255,255,.9);
        }
        .btn-email:hover { background: rgba(255,255,255,.18); }

        .footer-note {
            margin-top: 36px;
            font-size: 12px;
            color: rgba(255,255,255,.3);
        }

        @media (max-width: 480px) {
            .card { padding: 36px 24px; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>

        <p class="brand">Aguas Santa Catalina</p>

        <h1>Sitio en<br>mantenimiento</h1>

        <p class="message">{{ $mensaje }}</p>

        @if($fin)
        <div class="fin-box">
            <span class="fin-dot"></span>
            Estimamos volver: {{ \Carbon\Carbon::parse($fin)->locale('es')->isoFormat('dddd D [de] MMMM, HH:mm') }} h
        </div>
        @endif

        <div class="divider"></div>

        <p class="contact-title">¿Necesitas algo urgente?</p>
        <div class="contact-links">
            <a href="https://wa.me/56981493272?text=Hola!%20Vi%20que%20el%20sitio%20est%C3%A1%20en%20mantenci%C3%B3n%20y%20necesito%20ayuda."
               target="_blank" class="btn-whatsapp">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                WhatsApp
            </a>
            <a href="mailto:contacto@aguassantacatalina.cl" class="btn-email">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Email
            </a>
        </div>
    </div>

    <p class="footer-note">© {{ date('Y') }} Aguas Santa Catalina SpA · Santiago, Chile</p>
</body>
</html>
