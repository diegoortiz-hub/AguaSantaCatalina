<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Comprobaciones de salud del servicio.
 *
 * Existe porque /up sólo confirma que PHP arranca: ya pasó que el sitio
 * respondiera "bien" mientras devolvía 500 en todas las páginas porque la base
 * de datos estaba caída.
 *
 * Cada comprobación devuelve ['estado' => ok|aviso|falla, 'valor' => ..., 'nota' => ...]
 * y ninguna puede lanzar: si algo falla, eso mismo es el resultado.
 */
class EstadoSistema
{
    public const OK    = 'ok';
    public const AVISO = 'aviso';
    public const FALLA = 'falla';

    public function todo(): array
    {
        return [
            'base_datos'   => $this->baseDatos(),
            'almacenamiento' => $this->almacenamiento(),
            'cache'        => $this->cache(),
            'cola'         => $this->cola(),
            'correo'       => $this->correo(),
            'migraciones'  => $this->migraciones(),
            'entorno'      => $this->entorno(),
            'version'      => $this->version(),
        ];
    }

    /** El peor estado de todas las comprobaciones. */
    public function resumen(array $comprobaciones): string
    {
        $estados = array_column($comprobaciones, 'estado');

        if (in_array(self::FALLA, $estados, true)) {
            return self::FALLA;
        }

        return in_array(self::AVISO, $estados, true) ? self::AVISO : self::OK;
    }

    private function baseDatos(): array
    {
        try {
            $inicio = microtime(true);
            DB::select('select 1');
            $ms = round((microtime(true) - $inicio) * 1000, 1);

            $tamano = null;
            try {
                $fila = DB::selectOne(
                    'SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS mb
                     FROM information_schema.TABLES WHERE table_schema = ?',
                    [DB::getDatabaseName()]
                );
                $tamano = $fila->mb ?? null;
            } catch (\Throwable) {
                // El tamaño es informativo; no saberlo no es una falla.
            }

            return [
                'estado' => $ms > 250 ? self::AVISO : self::OK,
                'valor'  => "conectada · {$ms} ms",
                'nota'   => $tamano ? "{$tamano} MB en disco" : DB::getDatabaseName(),
            ];
        } catch (\Throwable $e) {
            return [
                'estado' => self::FALLA,
                'valor'  => 'sin conexión',
                'nota'   => Str::limit($e->getMessage(), 90),
            ];
        }
    }

    private function almacenamiento(): array
    {
        try {
            $ruta = storage_path('app');
            $escribible = is_writable($ruta);

            $libre  = disk_free_space($ruta);
            $total  = disk_total_space($ruta);
            $libreGb = round($libre / 1073741824, 1);
            $usado   = $total > 0 ? round((($total - $libre) / $total) * 100) : 0;

            $estado = match (true) {
                ! $escribible || $usado >= 95 => self::FALLA,
                $usado >= 85                  => self::AVISO,
                default                       => self::OK,
            };

            return [
                'estado' => $estado,
                'valor'  => $escribible ? "{$libreGb} GB libres" : 'no escribible',
                'nota'   => "{$usado}% del disco ocupado",
            ];
        } catch (\Throwable $e) {
            return ['estado' => self::FALLA, 'valor' => 'no verificable', 'nota' => Str::limit($e->getMessage(), 90)];
        }
    }

    private function cache(): array
    {
        try {
            $clave = 'estado_sistema_prueba';
            Cache::put($clave, 'ok', 10);
            $leido = Cache::get($clave);
            Cache::forget($clave);

            return $leido === 'ok'
                ? ['estado' => self::OK, 'valor' => config('cache.default'), 'nota' => 'escribe y lee']
                : ['estado' => self::FALLA, 'valor' => config('cache.default'), 'nota' => 'no devuelve lo que guarda'];
        } catch (\Throwable $e) {
            return ['estado' => self::FALLA, 'valor' => config('cache.default'), 'nota' => Str::limit($e->getMessage(), 90)];
        }
    }

    private function cola(): array
    {
        $driver = config('queue.default');

        if ($driver === 'sync') {
            return [
                'estado' => self::AVISO,
                'valor'  => 'sync',
                'nota'   => 'sin cola real: los correos se envían dentro de la petición y demoran el checkout',
            ];
        }

        try {
            $pendientes = DB::table('jobs')->count();
            $fallidos   = DB::table('failed_jobs')->count();

            $estado = match (true) {
                $fallidos > 0     => self::FALLA,
                $pendientes > 100 => self::AVISO,
                default           => self::OK,
            };

            return [
                'estado' => $estado,
                'valor'  => "{$pendientes} pendientes",
                'nota'   => $fallidos > 0 ? "{$fallidos} trabajos fallidos sin revisar" : 'sin trabajos fallidos',
            ];
        } catch (\Throwable $e) {
            return ['estado' => self::AVISO, 'valor' => $driver, 'nota' => Str::limit($e->getMessage(), 90)];
        }
    }

    private function correo(): array
    {
        $driver = config('mail.default');
        $desde  = config('mail.from.address');

        if ($driver === 'log' || $driver === 'array') {
            return [
                'estado' => self::FALLA,
                'valor'  => $driver,
                'nota'   => 'los correos no salen: quedan escritos en el log del servidor',
            ];
        }

        $sinConfigurar = ! $desde || Str::contains($desde, 'example.com');

        return [
            'estado' => $sinConfigurar ? self::AVISO : self::OK,
            'valor'  => $driver,
            'nota'   => $sinConfigurar ? 'falta definir el remitente' : "remitente: {$desde}",
        ];
    }

    private function migraciones(): array
    {
        try {
            $pendientes = collect(File::files(database_path('migrations')))
                ->map(fn ($f) => $f->getFilenameWithoutExtension())
                ->diff(DB::table('migrations')->pluck('migration'))
                ->values();

            return $pendientes->isEmpty()
                ? ['estado' => self::OK, 'valor' => 'al día', 'nota' => 'sin migraciones pendientes']
                : [
                    'estado' => self::FALLA,
                    'valor'  => $pendientes->count().' pendientes',
                    'nota'   => 'hay que ejecutar php artisan migrate',
                ];
        } catch (\Throwable $e) {
            return ['estado' => self::AVISO, 'valor' => 'no verificable', 'nota' => Str::limit($e->getMessage(), 90)];
        }
    }

    private function entorno(): array
    {
        $env   = config('app.env');
        $debug = config('app.debug');

        // En producción el modo depuración muestra la traza completa, con
        // credenciales de base de datos incluidas, a cualquiera que provoque un error.
        if ($env === 'production' && $debug) {
            return [
                'estado' => self::FALLA,
                'valor'  => 'producción con depuración activa',
                'nota'   => 'APP_DEBUG=true expone credenciales ante cualquier error',
            ];
        }

        return [
            'estado' => $env === 'production' ? self::OK : self::AVISO,
            'valor'  => $env.($debug ? ' · depuración activa' : ''),
            'nota'   => $env === 'production' ? 'configuración de producción' : 'entorno de desarrollo',
        ];
    }

    private function version(): array
    {
        // En el despliegue conviene escribir el commit en storage/app/VERSION.
        $archivo = storage_path('app/VERSION');

        if (is_file($archivo) && $contenido = trim((string) file_get_contents($archivo))) {
            return [
                'estado' => self::OK,
                'valor'  => Str::limit($contenido, 40, ''),
                'nota'   => 'desde storage/app/VERSION',
            ];
        }

        return [
            'estado' => self::AVISO,
            'valor'  => 'sin registrar',
            'nota'   => 'PHP '.PHP_VERSION.' · Laravel '.app()->version(),
        ];
    }
}
