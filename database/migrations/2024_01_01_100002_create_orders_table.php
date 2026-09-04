<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('nombre_cliente');
            $table->string('email_cliente');
            $table->string('telefono', 20)->nullable();
            $table->text('direccion')->nullable();
            $table->string('comuna')->nullable();
            $table->string('ciudad')->nullable();
            $table->enum('estado', ['pendiente', 'confirmado', 'enviado', 'entregado', 'cancelado'])->default('pendiente');
            $table->enum('metodo_pago', ['webpay', 'mercadopago', 'transferencia', 'whatsapp'])->default('whatsapp');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('costo_despacho', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
