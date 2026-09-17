<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoTributario extends Model
{
    protected $table = 'documentos_tributarios';

    protected $fillable = [
        'order_id', 'tipo', 'folio', 'fecha_emision',
        'neto', 'iva', 'exento', 'total',
        'estado', 'emisor', 'referencia',
        'xml_path', 'pdf_path', 'observaciones', 'anulado_at',
    ];

    protected function casts(): array
    {
        return [
            'fecha_emision' => 'date',
            'folio'         => 'integer',
            'neto'          => 'decimal:2',
            'iva'           => 'decimal:2',
            'exento'        => 'decimal:2',
            'total'         => 'decimal:2',
            'anulado_at'    => 'datetime',
        ];
    }

    public const TIPOS = [
        'boleta'       => 'Boleta electrónica',
        'factura'      => 'Factura electrónica',
        'nota_credito' => 'Nota de crédito',
    ];

    public const ESTADOS = [
        'pendiente' => 'Pendiente de emisión',
        'emitido'   => 'Emitido',
        'rechazado' => 'Rechazado',
        'anulado'   => 'Anulado',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopePendientes(Builder $query): Builder
    {
        return $query->where('estado', 'pendiente');
    }

    /** Sólo lo emitido cuenta para el libro de ventas. */
    public function scopeEmitidos(Builder $query): Builder
    {
        return $query->where('estado', 'emitido');
    }

    public function scopeDelMes(Builder $query, ?string $mes = null): Builder
    {
        $inicio = $mes
            ? \Carbon\Carbon::createFromFormat('Y-m', $mes)->startOfMonth()
            : now()->startOfMonth();

        return $query->whereBetween('fecha_emision', [
            $inicio->toDateString(),
            $inicio->copy()->endOfMonth()->toDateString(),
        ]);
    }

    public function etiquetaTipo(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function etiquetaEstado(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    /** Número de lectura: "Boleta N° 1042" o "Boleta sin folio". */
    public function numero(): string
    {
        $nombre = match ($this->tipo) {
            'boleta'       => 'Boleta',
            'factura'      => 'Factura',
            'nota_credito' => 'Nota de crédito',
            default        => 'Documento',
        };

        return $this->folio ? "{$nombre} N° {$this->folio}" : "{$nombre} sin folio";
    }

    public function estaEmitido(): bool
    {
        return $this->estado === 'emitido';
    }
}
