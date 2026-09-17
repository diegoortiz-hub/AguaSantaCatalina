@php
    $pedido = $documento->order;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $documento->numero() }}</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#1e293b;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:24px 12px;">
<tr><td align="center">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:12px;overflow:hidden;">

        <tr>
            <td style="background:#0A3D7A;padding:24px;color:#ffffff;">
                <p style="margin:0;font-size:12px;letter-spacing:1.5px;text-transform:uppercase;color:#bae6fd;">Aguas Santa Catalina</p>
                <h1 style="margin:6px 0 0;font-size:20px;font-weight:bold;">{{ $documento->numero() }}</h1>
            </td>
        </tr>

        <tr>
            <td style="padding:24px;">
                <p style="margin:0 0 16px;font-size:15px;line-height:1.55;">
                    Hola {{ $pedido->razon_social ?: $pedido->nombre_cliente }}, adjuntamos
                    {{ $documento->tipo === 'factura' ? 'la factura' : 'la boleta' }} de tu pedido
                    #{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}.
                </p>

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;border-collapse:collapse;">
                    <tr>
                        <td style="padding:8px 0;color:#64748b;border-bottom:1px solid #f1f5f9;">Documento</td>
                        <td style="padding:8px 0;text-align:right;font-weight:bold;border-bottom:1px solid #f1f5f9;">{{ $documento->etiquetaTipo() }}</td>
                    </tr>
                    @if($documento->folio)
                    <tr>
                        <td style="padding:8px 0;color:#64748b;border-bottom:1px solid #f1f5f9;">Folio</td>
                        <td style="padding:8px 0;text-align:right;font-weight:bold;border-bottom:1px solid #f1f5f9;">{{ $documento->folio }}</td>
                    </tr>
                    @endif
                    @if($documento->fecha_emision)
                    <tr>
                        <td style="padding:8px 0;color:#64748b;border-bottom:1px solid #f1f5f9;">Fecha de emisión</td>
                        <td style="padding:8px 0;text-align:right;border-bottom:1px solid #f1f5f9;">{{ $documento->fecha_emision->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                    @if($pedido->esFactura() && $pedido->rut_receptor)
                    <tr>
                        <td style="padding:8px 0;color:#64748b;border-bottom:1px solid #f1f5f9;">RUT</td>
                        <td style="padding:8px 0;text-align:right;border-bottom:1px solid #f1f5f9;">{{ $pedido->rut_receptor }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding:8px 0;color:#64748b;border-bottom:1px solid #f1f5f9;">Neto</td>
                        <td style="padding:8px 0;text-align:right;border-bottom:1px solid #f1f5f9;">${{ number_format($documento->neto, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;color:#64748b;border-bottom:1px solid #f1f5f9;">IVA 19%</td>
                        <td style="padding:8px 0;text-align:right;border-bottom:1px solid #f1f5f9;">${{ number_format($documento->iva, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="padding:12px 0;font-weight:bold;font-size:16px;">Total</td>
                        <td style="padding:12px 0;text-align:right;font-weight:bold;font-size:16px;color:#0A3D7A;">${{ number_format($documento->total, 0, ',', '.') }}</td>
                    </tr>
                </table>

                @if(! $documento->pdf_path)
                <p style="margin:16px 0 0;padding:12px;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;font-size:13px;color:#92400e;">
                    El archivo del documento se enviará en un correo aparte.
                </p>
                @endif

                <p style="margin:20px 0 0;font-size:13px;color:#64748b;line-height:1.55;">
                    Cualquier duda, escríbenos a {{ $ajustes->get('email') }} o al {{ $ajustes->get('telefono') }}.
                </p>
            </td>
        </tr>

        <tr>
            <td style="padding:16px 24px;background:#f8fafc;font-size:12px;color:#94a3b8;">
                {{ $ajustes->get('empresa') }} · RUT {{ $ajustes->get('rut') }}<br>
                {{ $ajustes->get('direccion') }}, {{ $ajustes->get('comuna') }}
            </td>
        </tr>
    </table>

</td></tr>
</table>

</body>
</html>
