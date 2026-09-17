<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    // Fuera de $fillable a propósito: el token identifica al pedido en la URL de
    // confirmación, así que no debe poder llegar desde una petición.
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
        'tipo_documento',
        'rut_receptor',
        'razon_social',
        'giro',
        'direccion_factura',
        'comuna_factura',
        'subtotal',
        'neto',
        'iva',
        'costo_despacho',
        'descuento',
        'total',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'       => 'decimal:2',
            'neto'           => 'decimal:2',
            'iva'            => 'decimal:2',
            'costo_despacho' => 'decimal:2',
            'descuento'      => 'decimal:2',
            'total'          => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $order) {
            $order->token ??= (string) Str::uuid();
        });
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

    public function documentos(): HasMany
    {
        return $this->hasMany(DocumentoTributario::class);
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

    public function esFactura(): bool
    {
        return $this->tipo_documento === 'factura';
    }

    public function documentoLabel(): string
    {
        return $this->esFactura() ? 'Factura electrónica' : 'Boleta electrónica';
    }

    /**
     * El documento que vale hoy para este pedido: el último que no esté
     * anulado. Si se anuló una boleta y se emitió otra, manda la segunda.
     */
    public function documentoVigente(): ?DocumentoTributario
    {
        return $this->documentos
            ->reject(fn (DocumentoTributario $d) => $d->estado === 'anulado')
            ->sortByDesc('id')
            ->first();
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
