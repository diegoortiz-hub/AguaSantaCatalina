<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Registro de boletas y facturas.
 *
 * Esta tabla NO emite documentos tributarios. En Chile un DTE lo emite un
 * contribuyente inscrito ante el SII, con certificado digital y folios CAF; el
 * sistema registra lo que se emitió y guarda el PDF y el XML.
 *
 * `emisor` dice quién lo generó: "manual" cuando se registra a mano después de
 * emitirlo en el portal del SII, o el nombre del proveedor cuando se integre
 * una API. `referencia` guarda el identificador que devuelva ese proveedor.
 *
 * Un pedido puede tener más de un documento: si uno se anula, el siguiente
 * queda registrado al lado y no se pisa el histórico.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos_tributarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            $table->enum('tipo', ['boleta', 'factura', 'nota_credito']);
            $table->unsignedBigInteger('folio')->nullable();
            $table->date('fecha_emision')->nullable();

            $table->decimal('neto', 10, 2)->default(0);
            $table->decimal('iva', 10, 2)->default(0);
            $table->decimal('exento', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->enum('estado', ['pendiente', 'emitido', 'rechazado', 'anulado'])->default('pendiente');
            $table->string('emisor', 50)->default('manual');
            $table->string('referencia', 191)->nullable();

            $table->string('xml_path')->nullable();
            $table->string('pdf_path')->nullable();

            $table->text('observaciones')->nullable();
            $table->timestamp('anulado_at')->nullable();
            $table->timestamps();

            // Dos documentos del mismo tipo no pueden compartir folio. Los
            // folios en null no chocan entre sí, que es justo lo que se
            // necesita mientras el documento está pendiente de emisión.
            $table->unique(['tipo', 'folio']);

            $table->index(['estado', 'fecha_emision']);
            $table->index('fecha_emision');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_tributarios');
    }
};
