<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('delivery_days');
            $table->unsignedInteger('free_shipping_threshold')->default(15000);
            $table->unsignedInteger('standard_shipping_cost')->default(2500);
            $table->string('delivery_time');
            $table->boolean('activo')->default(true);
            $table->unsignedTinyInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communes');
    }
};
