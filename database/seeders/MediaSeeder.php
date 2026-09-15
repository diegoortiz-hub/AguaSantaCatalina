<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Copia las imágenes de marca versionadas en el repositorio al disco público.
 *
 * Las subidas desde el panel viven en storage/app/public, que está fuera de git:
 * en un servidor nuevo ese directorio nace vacío y el catálogo se queda sin
 * fotos. Teniendo los archivos en el repositorio, un `db:seed` los reinstala.
 */
class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $copiados = 0;

        foreach (['productos', 'banners'] as $carpeta) {
            $origen = database_path("seeders/media/{$carpeta}");

            if (! File::isDirectory($origen)) {
                continue;
            }

            foreach (File::files($origen) as $archivo) {
                $destino = "{$carpeta}/{$archivo->getFilename()}";

                // No sobrescribe: si alguien reemplazó la foto desde el panel,
                // esa versión manda.
                if (Storage::disk('public')->exists($destino)) {
                    continue;
                }

                Storage::disk('public')->put($destino, File::get($archivo->getPathname()));
                $copiados++;
            }
        }

        $this->command->info("MediaSeeder: {$copiados} imágenes instaladas en el disco público.");
    }
}
