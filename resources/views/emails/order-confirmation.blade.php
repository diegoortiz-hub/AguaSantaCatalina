<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido confirmado</title>
    <style>
        body { margin: 0; padding: 0; background: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #1e293b; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #0A3D7A 0%, #1E6FBF 100%); padding: 32px 40px; text-align: center; }
        .header h1 { margin: 0; color: #fff; font-size: 22px; font-weight: 800; letter-spacing: -.3px; }
        .header p { margin: 6px 0 0; color: rgba(255,255,255,.75); font-size: 14px; }
        .badge { display: inline-block; margin: 24px auto 0; background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.25); color: #fff; border-radius: 999px; padding: 6px 20px; font-size: 13px; font-weight: 700; letter-spacing: .5px; }
        .body { padding: 32px 40px; }
        .greeting { font-size: 16px; margin: 0 0 20px; }
        .section-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #64748b; margin: 0 0 10px; }
        .items-table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .items-table th { text-align: left; padding: 8px 12px; background: #f8fafc; color: #64748b; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; }
        .items-table td { padding: 10px 12px; border-top: 1px solid #f1f5f9; }
        .items-table td:last-child { text-align: right; font-weight: 600; }
        .totals { margin-top: 16px; border-top: 2px solid #e2e8f0; padding-top: 12px; }
        .totals-row { display: flex; justify-content: space-between; font-size: 14px; padding: 4px 0; color: #475569; }
        .totals-row.grand { font-size: 17px; font-weight: 800; color: #0A3D7A; border-top: 1px solid #e2e8f0; margin-top: 6px; padding-top: 10px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 24px; }
        .info-box { background: #f8fafc; border-radius: 10px; padding: 14px 16px; }
        .info-box .label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #94a3b8; margin-bottom: 4px; }
        .info-box .value { font-size: 14px; font-weight: 600; color: #1e293b; }
        .transfer-box { margin-top: 24px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 16px 20px; }
        .transfer-box h3 { margin: 0 0 10px; font-size: 14px; font-weight: 700; color: #92400e; }
        .transfer-box p { margin: 3px 0; font-size: 13px; color: #78350f; }
        .cta { text-align: center; margin-top: 28px; }
        .cta a { display: inline-block; background: #1a56c4; color: #fff !important; text-decoration: none; padding: 12px 32px; border-radius: 10px; font-size: 14px; font-weight: 700; }
        .footer { background: #f8fafc; padding: 20px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.6; }
        @media (max-width:480px) {
            .body { padding: 24px 20px; }
            .info-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    {{-- Header --}}
    <div class="header">
        <h1>Aguas Santa Catalina</h1>
        <p>Agua purificada · Entrega a domicilio</p>
        <div class="badge">Pedido #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
    </div>

    {{-- Body --}}
    <div class="body">
        <p class="greeting">
            Hola <strong>{{ $order->nombre_cliente }}</strong>, ¡gracias por tu compra!<br>
            Recibimos tu pedido y lo estamos procesando. Te avisaremos cuando esté en camino.
        </p>

        {{-- Productos --}}
        <p class="section-title">Detalle del pedido</p>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th style="text-align:center">Cant.</th>
                    <th style="text-align:right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->nombre_producto }}</td>
                    <td style="text-align:center">{{ $item->cantidad }}</td>
                    <td>${{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totales --}}
        <div class="totals">
            <div class="totals-row">
                <span>Subtotal</span>
                <span>${{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="totals-row">
                <span>Despacho</span>
                <span>{{ $order->costo_despacho == 0 ? 'Gratis' : '$'.number_format($order->costo_despacho, 0, ',', '.') }}</span>
            </div>
            @if($order->descuento > 0)
            <div class="totals-row" style="color:#16a34a;">
                <span>Descuento</span>
                <span>-${{ number_format($order->descuento, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="totals-row grand">
                <span>Total a pagar</span>
                <span>${{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Info boxes --}}
        <div class="info-grid">
            <div class="info-box">
                <div class="label">Método de pago</div>
                <div class="value">{{ $order->metodoPagoLabel() }}</div>
            </div>
            @if($order->direccion)
            <div class="info-box">
                <div class="label">Dirección de entrega</div>
                <div class="value">{{ $order->direccion }}, {{ $order->comuna }}</div>
            </div>
            @endif
        </div>

        {{-- Instrucciones transferencia --}}
        @if($order->metodo_pago === 'transferencia')
        <div class="transfer-box">
            <h3>Instrucciones de transferencia</h3>
            <p><strong>Banco BCI</strong> · Cuenta Corriente</p>
            <p><strong>Titular:</strong> Aguas Santa Catalina SpA</p>
            <p><strong>RUT:</strong> 76.543.210-1</p>
            <p><strong>Monto:</strong> ${{ number_format($order->total, 0, ',', '.') }}</p>
            <p style="margin-top:8px;">Envía el comprobante indicando el número de pedido <strong>#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</strong> a <strong>pedidos@aguassantacatalina.cl</strong></p>
        </div>
        @endif

        <div class="cta">
            <a href="{{ url('/pedido/'.$order->id.'/confirmacion') }}">Ver detalle de mi pedido →</a>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>
            Aguas Santa Catalina SpA · Santiago, Chile<br>
            ¿Tienes dudas? Escríbenos a <a href="mailto:contacto@aguassantacatalina.cl" style="color:#1a56c4;">contacto@aguassantacatalina.cl</a><br>
            o al WhatsApp <strong>+56 9 8149 3272</strong> · L–S 8–20h
        </p>
    </div>
</div>
</body>
</html>
