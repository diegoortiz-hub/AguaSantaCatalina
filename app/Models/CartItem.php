<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'product_id',
        'cantidad',
    ];

    // ── Relaciones ────────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ── Helpers ───────────────────────────────────────────────

    public function subtotal(): float
    {
        return $this->product->precio * $this->cantidad;
    }
}
