<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * El dashboard filtra pedidos por estado y por rango de fechas en casi todas
     * sus consultas, y la tabla sólo tenía la llave primaria y la del usuario:
     * cada carga del panel recorría orders completa varias veces.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['estado', 'created_at'], 'orders_estado_created_idx');
            $table->index('created_at', 'orders_created_idx');
            // Para agrupar compras por cliente: el checkout permite comprar sin
            // cuenta, así que la recompra se calcula por correo y no por user_id.
            $table->index('email_cliente', 'orders_email_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['activo', 'category_id'], 'products_activo_categoria_idx');
            $table->index(['activo', 'destacado'], 'products_activo_destacado_idx');
        });

        Schema::table('order_items', function (Blueprint $table) {
            // Ranking de productos más vendidos.
            $table->index('product_id', 'order_items_product_idx');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_estado_created_idx');
            $table->dropIndex('orders_created_idx');
            $table->dropIndex('orders_email_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_activo_categoria_idx');
            $table->dropIndex('products_activo_destacado_idx');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_product_idx');
        });
    }
};
