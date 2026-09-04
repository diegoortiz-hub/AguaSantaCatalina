<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->enum('tipo', ['porcentaje', 'monto'])->default('porcentaje');
            $table->decimal('descuento', 10, 2);
            $table->decimal('minimo_compra', 10, 2)->default(0);
            $table->integer('maximo_usos')->nullable();
            $table->integer('usos_actuales')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamp('vence_en')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
