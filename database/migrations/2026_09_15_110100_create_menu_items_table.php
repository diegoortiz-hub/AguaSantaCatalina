<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            // Las ubicaciones son fijas porque cada una corresponde a un lugar
            // del layout; lo que se administra son los enlaces dentro de cada una.
            $table->enum('ubicacion', ['header', 'footer_1', 'footer_2', 'footer_3']);
            $table->string('etiqueta');
            // ruta      -> nombre de ruta de Laravel (lista blanca)
            // categoria -> catálogo filtrado por el slug de una categoría
            // pagina    -> slug de una página editable
            // url       -> enlace externo o relativo
            $table->enum('tipo', ['ruta', 'categoria', 'pagina', 'url']);
            $table->string('destino');
            $table->boolean('nueva_pestana')->default(false);
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['ubicacion', 'activo', 'orden'], 'menu_ubicacion_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
