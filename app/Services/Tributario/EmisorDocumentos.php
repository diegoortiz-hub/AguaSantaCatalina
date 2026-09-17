<?php

namespace App\Services\Tributario;

use App\Models\DocumentoTributario;
use App\Models\Order;

/**
 * Quién genera el documento tributario de un pedido.
 *
 * Existe como interfaz porque hay dos caminos y la decisión no está tomada:
 * emitir a mano en el portal del SII y registrar acá, o llamar a la API de un
 * proveedor autorizado. Lo que sigue del sistema —el registro, el listado del
 * panel, el correo al cliente, el libro de ventas— es igual en los dos casos,
 * así que sólo cambia esta pieza.
 */
interface EmisorDocumentos
{
    /** Nombre corto que queda guardado en el documento. */
    public function nombre(): string;

    /**
     * Si es false, el documento queda pendiente y alguien tiene que emitirlo
     * fuera del sistema y registrar el folio.
     */
    public function emiteAutomaticamente(): bool;

    /**
     * Deja registrado el documento que corresponde al pedido.
     *
     * No debe lanzar excepción por problemas del emisor: un pedido ya pagado no
     * se cae porque el documento falle. Si algo sale mal, el documento queda en
     * estado rechazado con la observación, y el panel lo muestra.
     */
    public function emitir(Order $order): DocumentoTributario;
}
