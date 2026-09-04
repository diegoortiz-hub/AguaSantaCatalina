<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    protected $fillable = [
        'name',
        'delivery_days',
        'free_shipping_threshold',
        'standard_shipping_cost',
        'delivery_time',
        'activo',
        'orden',
    ];

    protected $casts = [
        'activo'                  => 'boolean',
        'free_shipping_threshold' => 'integer',
        'standard_shipping_cost'  => 'integer',
    ];

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function scopeOrdenado($query)
    {
        return $query->orderBy('orden');
    }

    public function shippingCostFor(int $subtotal): int
    {
        if ($subtotal === 0) {
            return 0;
        }

        return $subtotal >= $this->free_shipping_threshold ? 0 : $this->standard_shipping_cost;
    }
}
