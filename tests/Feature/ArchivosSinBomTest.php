<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Trece archivos del proyecto quedaron con BOM UTF-8 tras editarlos con
 * PowerShell (Set-Content -Encoding UTF8 lo agrega en Windows PowerShell 5.1).
 *
 * El daño no es visible pero es real: en un .php el BOM se emite antes de la
 * etiqueta de apertura, lo que rompe cualquier cabecera o redirección posterior
 * con "headers already sent"; en un .blade.php aparece como un carácter
 * invisible al inicio del HTML. Un archivo llegó a romper la aplicación con
 * "Namespace declaration statement has to be the very first statement".
 *
 * Esta prueba existe para que el problema no vuelva sin que nadie lo note.
 */
class ArchivosSinBomTest extends TestCase
{
    private const BOM = "\xEF\xBB\xBF";

    public static function directorios(): array
    {
        return [
            'app'       => ['app'],
            'config'    => ['config'],
            'database'  => ['database'],
            'lang'      => ['lang'],
            'routes'    => ['routes'],
            'tests'     => ['tests'],
            'vistas'    => ['resources/views'],
            'bootstrap' => ['bootstrap'],
        ];
    }

    #[DataProvider('directorios')]
    public function test_ningun_archivo_php_empieza_con_bom(string $directorio): void
    {
        $ruta = base_path($directorio);

        if (! is_dir($ruta)) {
            $this->markTestSkipped("No existe {$directorio}");
        }

        $conBom = [];

        $archivos = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($ruta, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($archivos as $archivo) {
            if (! $archivo->isFile() || $archivo->getExtension() !== 'php') {
                continue;
            }

            $manejador = fopen($archivo->getPathname(), 'rb');
            $inicio    = fread($manejador, 3);
            fclose($manejador);

            if ($inicio === self::BOM) {
                $conBom[] = str_replace(base_path().DIRECTORY_SEPARATOR, '', $archivo->getPathname());
            }
        }

        $this->assertSame([], $conBom, sprintf(
            "Estos archivos de %s tienen BOM UTF-8 y hay que guardarlos sin él:\n  %s",
            $directorio,
            implode("\n  ", $conBom)
        ));
    }
}
