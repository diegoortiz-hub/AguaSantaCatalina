<?php

namespace App\Support;

/**
 * Desglose del IVA.
 *
 * Los precios del catálogo se muestran con IVA incluido, que es como se venden
 * al consumidor. El documento tributario, en cambio, declara el neto y el IVA
 * por separado. Así que aquí no se suma nada: se parte el bruto.
 *
 * El neto se redondea y el IVA sale de la diferencia, nunca de multiplicar otra
 * vez. Si se calcularan los dos por separado, la suma podría quedar un peso
 * arriba o abajo del total cobrado, y un documento que no cuadra con el cobro
 * es un problema contable.
 */
class Iva
{
    /** Tasa vigente en Chile. */
    public const TASA = 0.19;

    /** @return array{neto: int, iva: int, total: int} */
    public static function desglosar(float|int $bruto): array
    {
        $total = (int) round($bruto);

        if ($total <= 0) {
            return ['neto' => 0, 'iva' => 0, 'total' => max($total, 0)];
        }

        $neto = (int) round($total / (1 + self::TASA));

        return [
            'neto'  => $neto,
            'iva'   => $total - $neto,
            'total' => $total,
        ];
    }

    /** Lo contrario, para cotizaciones que se piensan en neto. */
    public static function agregar(float|int $neto): int
    {
        return (int) round($neto * (1 + self::TASA));
    }
}
