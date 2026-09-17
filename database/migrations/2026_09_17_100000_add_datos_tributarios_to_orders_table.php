<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Datos que el pedido necesita para que se le pueda emitir un documento.
 *
 * Los precios del catálogo incluyen IVA, así que el neto y el IVA no se suman
 * al total: se desglosan de él. Se guardan en el pedido y no se recalculan al
 * leer, porque la tasa puede cambiar y un documento ya emitido no se reescribe.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('tipo_documento', ['boleta', 'factura'])->default('boleta')->after('metodo_pago');

            $table->decimal('neto', 10, 2)->default(0)->after('subtotal');
            $table->decimal('iva', 10, 2)->default(0)->after('neto');

            // Receptor de la factura. Para boleta van en null: la boleta no
            // identifica al comprador.
            $table->string('rut_receptor', 12)->nullable()->after('tipo_documento');
            $table->string('razon_social')->nullable()->after('rut_receptor');
            $table->string('giro')->nullable()->after('razon_social');
            $table->string('direccion_factura', 500)->nullable()->after('giro');
            $table->string('comuna_factura', 100)->nullable()->after('direccion_factura');

            $table->index('tipo_documento');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['tipo_documento']);
            $table->dropColumn([
                'tipo_documento', 'neto', 'iva',
                'rut_receptor', 'razon_social', 'giro',
                'direccion_factura', 'comuna_factura',
            ]);
        });
    }
};
