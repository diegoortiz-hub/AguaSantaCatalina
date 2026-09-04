<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'nombre',
        'slug',
        'descripcion',
        'precio',
        'precio_original',
        'stock',
        'stock_minimo',
        'sku',
        'imagen',
        'imagenes',
        'badge',
        'badge_color',
        'destacado',
        'activo',
        'specs',
    ];

    protected function casts(): array
    {
        return [
            'imagenes'   => 'array',
            'specs'      => 'array',
            'precio'     => 'decimal:2',
            'precio_original' => 'decimal:2',
            'destacado'  => 'boolean',
            'activo'     => 'boolean',
        ];
    }

    // ── Relaciones ────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function scopeDestacado($query)
    {
        return $query->where('destacado', true);
    }

    public function scopeConStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    // ── Helpers ───────────────────────────────────────────────

    public function tieneDescuento(): bool
    {
        return $this->precio_original && $this->precio_original > $this->precio;
    }

    public function porcentajeDescuento(): int
    {
        if (! $this->tieneDescuento()) {
            return 0;
        }
        return (int) round((1 - $this->precio / $this->precio_original) * 100);
    }

    public function stockBajo(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }
}
