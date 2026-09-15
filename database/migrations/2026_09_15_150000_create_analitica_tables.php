<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Analítica propia, agregada por día en vez de una fila por visita.
     *
     * Guardar cada carga de página haría crecer la tabla sin control; así
     * `visitas` tiene como máximo una fila por ruta y día (unas decenas), y
     * `visitantes` una por persona y día, que se poda a los 90 días.
     *
     * No se guarda IP ni user agent: el token es un hash irreversible que
     * incluye la fecha, así que cambia cada día y no permite seguir a nadie
     * entre jornadas ni reconstruir el dato original (Ley 19.628).
     */
    public function up(): void
    {
        Schema::create('visitas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('ruta', 191);
            $table->unsignedInteger('visitas')->default(0);

            $table->unique(['fecha', 'ruta']);
            $table->index('fecha');
        });

        Schema::create('visitantes', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->char('token', 64);

            $table->unique(['fecha', 'token']);
            $table->index('fecha');
        });

        Schema::create('referencias', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('origen', 191);
            $table->unsignedInteger('visitas')->default(0);

            $table->unique(['fecha', 'origen']);
            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referencias');
        Schema::dropIfExists('visitantes');
        Schema::dropIfExists('visitas');
    }
};
