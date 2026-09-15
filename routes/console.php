<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Requiere que el servidor ejecute `php artisan schedule:run` cada minuto.
// Sin eso nada de esto corre y las tablas crecen sin límite.
Schedule::command('mantencion:limpiar')->dailyAt('04:30');
