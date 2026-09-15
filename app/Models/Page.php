<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    protected $fillable = [
        'titulo',
        'slug',
        'bajada',
        'contenido',
        'meta_descripcion',
        'activo',
    ];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saving(function (self $page) {
            $page->slug = Str::slug($page->slug ?: $page->titulo);
        });
    }

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }
}
