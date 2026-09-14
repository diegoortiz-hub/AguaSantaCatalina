<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recupera tu contraseña</title>
    <style>
        body { margin:0; padding:24px 12px; background:#f1f5f9; font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif; color:#1e293b; }
        .wrapper { max-width:520px; margin:0 auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 2px 12px rgba(15,23,42,.08); }
        .header { background:linear-gradient(135deg,#0A3D7A 0%,#1E6FBF 100%); padding:28px 40px; text-align:center; }
        .header img { display:block; height:46px; width:auto; margin:0 auto; border:0; }
        .body { padding:32px 40px; }
        .body h1 { margin:0 0 14px; font-size:20px; color:#0A3D7A; }
        .body p { margin:0 0 16px; font-size:14px; line-height:1.65; color:#475569; }
        .cta { text-align:center; margin:26px 0; }
        .cta a { display:inline-block; background:#1a56c4; color:#fff !important; text-decoration:none; padding:13px 34px; border-radius:10px; font-size:14px; font-weight:700; }
        .aviso { background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px; font-size:12px; color:#64748b; line-height:1.6; }
        .enlace { font-size:11px; color:#94a3b8; word-break:break-all; margin-top:14px; }
        .footer { background:#f8fafc; padding:18px 40px; text-align:center; border-top:1px solid #e2e8f0; }
        .footer p { margin:0; font-size:12px; color:#94a3b8; line-height:1.6; }
        @media (max-width:480px) { .body { padding:24px 20px; } .header { padding:22px 20px; } }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <img src="{{ asset('images/logo-blanco.png') }}" alt="Aguas Santa Catalina">
    </div>

    <div class="body">
        <h1>Hola{{ $nombre ? ', '.$nombre : '' }}</h1>
        <p>
            Recibimos una solicitud para crear una nueva contraseña de tu cuenta en
            Aguas Santa Catalina. Puedes hacerlo desde el siguiente botón:
        </p>

        <div class="cta">
            <a href="{{ $url }}">Crear nueva contraseña</a>
        </div>

        <div class="aviso">
            El enlace vence en {{ $minutos }} minutos y sólo se puede usar una vez.
            Si no pediste este cambio, puedes ignorar este correo: tu contraseña
            actual sigue funcionando.
        </div>

        <p class="enlace">
            Si el botón no funciona, copia y pega esta dirección en tu navegador:<br>{{ $url }}
        </p>
    </div>

    <div class="footer">
        <p>
            Aguas Santa Catalina · Santiago, Chile<br>
            Si necesitas ayuda, escríbenos al WhatsApp {{ $ajustes->get('telefono') }}
        </p>
    </div>
</div>
</body>
</html>
