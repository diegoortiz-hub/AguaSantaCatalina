<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route as RouteFacade;

class MenuItem extends Model
{
    /** Rutas de la tienda que un enlace puede apuntar. Lista blanca: evita que
     *  alguien escriba el nombre de una ruta de admin o una inexistente. */
    public const RUTAS_PERMITIDAS = [
        'home'            => 'Inicio',
        'productos.index' => 'Catálogo de productos',
        'ofertas'         => 'Ofertas',
        'empresas'        => 'Empresas',
        'nosotros'        => 'Nosotros',
        'contacto'        => 'Contacto',
        'carrito'         => 'Carrito',
        'login'           => 'Ingresar',
    ];

    public const UBICACIONES = [
        'header'   => 'Menú principal (header)',
        'footer_1' => 'Footer · columna 1',
        'footer_2' => 'Footer · columna 2',
        'footer_3' => 'Footer · columna 3',
    ];

    protected $fillable = [
        'ubicacion',
        'etiqueta',
        'tipo',
        'destino',
        'nueva_pestana',
        'orden',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo'        => 'boolean',
            'nueva_pestana' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // El layout pide los menús en cada página, así que van en caché y se
        // invalidan al guardar desde el panel.
        static::saved(fn () => static::olvidarCache());
        static::deleted(fn () => static::olvidarCache());
    }

    public static function olvidarCache(): void
    {
        foreach (array_keys(self::UBICACIONES) as $ubicacion) {
            Cache::forget("menu.{$ubicacion}");
        }
    }

    /**
     * Enlaces activos de una ubicación, listos para pintar.
     *
     * Se cachean arreglos y no la colección: serializar modelos de Eloquent
     * devuelve __PHP_Incomplete_Class si el payload no se restaura tal cual,
     * y el menú deja de funcionar en todo el sitio.
     */
    public static function de(string $ubicacion): Collection
    {
        $filas = Cache::remember("menu.{$ubicacion}", now()->addHours(12), fn () => static::query()
            ->where('ubicacion', $ubicacion)
            ->where('activo', true)
            ->orderBy('orden')
            ->orderBy('id')
            ->get()
            ->map->only(['id', 'ubicacion', 'etiqueta', 'tipo', 'destino', 'nueva_pestana', 'orden', 'activo'])
            ->all());

        return static::hydrate($filas);
    }

    /** URL final del enlace, o null si apunta a algo que ya no existe. */
    public function url(): ?string
    {
        return match ($this->tipo) {
            'url'       => $this->destino,
            'categoria' => Category::where('slug', $this->destino)->where('activo', true)->exists()
                            ? route('productos.index', ['categoria' => $this->destino])
                            : null,
            'pagina'    => Page::activo()->where('slug', $this->destino)->exists()
                            ? url('/'.$this->destino)
                            : null,
            'ruta'      => array_key_exists($this->destino, self::RUTAS_PERMITIDAS) && RouteFacade::has($this->destino)
                            ? route($this->destino)
                            : null,
            default     => null,
        };
    }

    public function destinoLegible(): string
    {
        return match ($this->tipo) {
            'ruta'      => self::RUTAS_PERMITIDAS[$this->destino] ?? $this->destino,
            'categoria' => 'Catálogo · '.$this->destino,
            'pagina'    => '/'.$this->destino,
            'url'       => $this->destino,
            default     => $this->destino,
        };
    }
}
