<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('token')->nullable()->unique()->after('id');
        });

        // Los pedidos que ya existen necesitan token para seguir siendo consultables.
        DB::table('orders')->whereNull('token')->orderBy('id')->each(function ($order) {
            DB::table('orders')->where('id', $order->id)->update(['token' => (string) Str::uuid()]);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['token']);
            $table->dropColumn('token');
        });
    }
};
