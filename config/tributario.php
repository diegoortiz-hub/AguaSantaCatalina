<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Emisor de documentos
    |--------------------------------------------------------------------------
    |
    | "manual" registra el documento y deja el folio para después, que es lo
    | que corresponde mientras la emisión se haga en el portal del SII o la
    | lleve el contador.
    |
    | Cuando se contrate un proveedor autorizado, se agrega su clase al arreglo
    | de abajo y se cambia este valor. El resto del sistema no se toca.
    |
    */

    'emisor' => env('TRIBUTARIO_EMISOR', 'manual'),

    'emisores' => [
        'manual' => App\Services\Tributario\EmisorManual::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Tasa de IVA
    |--------------------------------------------------------------------------
    |
    | Está en App\Support\Iva::TASA. No se saca a configuración a propósito:
    | un documento ya emitido guarda su propio desglose, así que cambiar la
    | tasa nunca debe reescribir lo que ya se declaró.
    |
    */

];
