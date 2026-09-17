<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * RUT chileno con dígito verificador.
 *
 * Se valida de verdad y no sólo el formato: un RUT mal tipeado que entra a una
 * factura obliga a anularla y emitirla de nuevo, así que conviene rechazarlo
 * en el formulario. El dígito se calcula con módulo 11.
 */
class Rut implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $limpio = self::normalizar((string) $value);

        if (! preg_match('/^\d{7,8}[0-9K]$/', $limpio)) {
            $fail('El :attribute no tiene un formato válido. Ejemplo: 12.345.678-5');

            return;
        }

        $cuerpo = substr($limpio, 0, -1);
        $digito = substr($limpio, -1);

        if ($digito !== self::digitoVerificador($cuerpo)) {
            $fail('El :attribute no es válido: el dígito verificador no corresponde.');
        }
    }

    /** Deja sólo dígitos y la K final, en mayúscula. */
    public static function normalizar(string $rut): string
    {
        return strtoupper(preg_replace('/[^0-9kK]/', '', $rut) ?? '');
    }

    /** Formato de lectura: 12.345.678-5 */
    public static function formatear(string $rut): string
    {
        $limpio = self::normalizar($rut);

        if (strlen($limpio) < 2) {
            return $rut;
        }

        $cuerpo = substr($limpio, 0, -1);
        $digito = substr($limpio, -1);

        return number_format((int) $cuerpo, 0, '', '.').'-'.$digito;
    }

    public static function digitoVerificador(string $cuerpo): string
    {
        $suma   = 0;
        $factor = 2;

        foreach (array_reverse(str_split($cuerpo)) as $digito) {
            $suma += ((int) $digito) * $factor;
            $factor = $factor === 7 ? 2 : $factor + 1;
        }

        $resto = 11 - ($suma % 11);

        return match ($resto) {
            11      => '0',
            10      => 'K',
            default => (string) $resto,
        };
    }
}
