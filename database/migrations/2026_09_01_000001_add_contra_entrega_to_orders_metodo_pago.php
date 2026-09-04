<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN metodo_pago ENUM('webpay','mercadopago','transferencia','whatsapp','contra_entrega') NOT NULL DEFAULT 'whatsapp'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN metodo_pago ENUM('webpay','mercadopago','transferencia','whatsapp') NOT NULL DEFAULT 'whatsapp'");
    }
};
