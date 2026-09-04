<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nombre_cliente',
        'email_cliente',
        'telefono',
        'direccion',
        'comuna',
        'ciudad',
        'estado',
        'metodo_pago',
        'subtotal',
        'costo_despacho',
        'descuento',
        'total',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'       => 'decimal:2',
            'costo_despacho' => 'decimal:2',
            'descuento'      => 'decimal:2',
            'total'          => 'decimal:2',
        ];
    }

    // ── Relaciones ────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopePorEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }

    // ── Helpers ───────────────────────────────────────────────

    public function estadoLabel(): string
    {
        return match ($this->estado) {
            'pendiente'  => 'Pendiente',
            'confirmado' => 'Confirmado',
            'enviado'    => 'Enviado',
            'entregado'  => 'Entregado',
            'cancelado'  => 'Cancelado',
            default      => ucfirst($this->estado),
        };
    }

    public function metodoPagoLabel(): string
    {
        return match ($this->metodo_pago) {
            'webpay'         => 'Webpay Plus',
            'mercadopago'    => 'MercadoPago',
            'transferencia'  => 'Transferencia Bancaria',
            'whatsapp'       => 'WhatsApp',
            'contra_entrega' => 'Contra Entrega',
            default          => ucfirst($this->metodo_pago),
        };
    }
}
