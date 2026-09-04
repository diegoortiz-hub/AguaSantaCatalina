<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'tipo',
        'descuento',
        'minimo_compra',
        'maximo_usos',
        'usos_actuales',
        'activo',
        'vence_en',
    ];

    protected function casts(): array
    {
        return [
            'descuento'     => 'decimal:2',
            'minimo_compra' => 'decimal:2',
            'activo'        => 'boolean',
            'vence_en'      => 'datetime',
        ];
    }

    // ── Helpers ───────────────────────────────────────────────

    public function esValido(): bool
    {
        if (! $this->activo) {
            return false;
        }
        if ($this->vence_en && $this->vence_en->isPast()) {
            return false;
        }
        if ($this->maximo_usos !== null && $this->usos_actuales >= $this->maximo_usos) {
            return false;
        }
        return true;
    }

    public function calcularDescuento(float $subtotal): float
    {
        if ($subtotal < $this->minimo_compra) {
            return 0;
        }
        if ($this->tipo === 'porcentaje') {
            return round($subtotal * ($this->descuento / 100), 2);
        }
        return min($this->descuento, $subtotal);
    }
}
