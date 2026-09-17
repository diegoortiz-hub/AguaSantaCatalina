<?php

namespace App\Services\Tributario;

use App\Models\DocumentoTributario;
use App\Models\Order;
use App\Support\Iva;

/**
 * Emisor por omisión: no emite.
 *
 * Deja el documento anotado con el desglose ya calculado y el estado en
 * pendiente. Después alguien lo emite donde corresponda —el portal MIPYME del
 * SII, el sistema del contador— y registra el folio en el panel.
 *
 * Esto no es un parche a la espera de "lo bueno": mientras el volumen sea de
 * unos pocos pedidos al día, es el flujo correcto. Y es lo único que se puede
 * hacer hoy con honestidad, porque emitir un DTE exige estar inscrito ante el
 * SII con certificado digital y folios CAF, que es algo del contribuyente y no
 * del software.
 */
class EmisorManual implements EmisorDocumentos
{
    public function nombre(): string
    {
        return 'manual';
    }

    public function emiteAutomaticamente(): bool
    {
        return false;
    }

    public function emitir(Order $order): DocumentoTributario
    {
        // Los precios incluyen IVA, así que el desglose se saca del total
        // cobrado y no se suma nada encima.
        $desglose = Iva::desglosar((float) $order->total);

        return $order->documentos()->create([
            'tipo'          => $order->esFactura() ? 'factura' : 'boleta',
            'neto'          => $desglose['neto'],
            'iva'           => $desglose['iva'],
            'exento'        => 0,
            'total'         => $desglose['total'],
            'estado'        => 'pendiente',
            'emisor'        => $this->nombre(),
            'observaciones' => 'Pendiente de emisión. Registra el folio cuando se emita.',
        ]);
    }
}
