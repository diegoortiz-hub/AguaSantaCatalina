<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Traslada a base de datos la navegación que estaba escrita a mano en el layout,
 * de modo que pase a administrarse desde el panel sin que cambie nada visible.
 */
class NavegacionSeeder extends Seeder
{
    public function run(): void
    {
        $paginas = [
            [
                'titulo'           => 'Términos y Condiciones',
                'slug'             => 'terminos-y-condiciones',
                'bajada'           => 'Condiciones de compra y uso del sitio',
                'meta_descripcion' => 'Términos y condiciones de compra de Aguas Santa Catalina.',
                'contenido'        => '<p><strong>Pendiente de redacción.</strong> Este texto debe ser revisado por un abogado antes de publicar la tienda: la Ley 19.496 de protección al consumidor exige informar condiciones de compra, plazos de entrega, garantías y derecho a retracto.</p><p>Reemplaza este contenido desde el panel de administración, en Páginas.</p>',
            ],
            [
                'titulo'           => 'Política de Privacidad',
                'slug'             => 'politica-de-privacidad',
                'bajada'           => 'Cómo tratamos tus datos personales',
                'meta_descripcion' => 'Política de privacidad y tratamiento de datos de Aguas Santa Catalina.',
                'contenido'        => '<p><strong>Pendiente de redacción.</strong> La Ley 19.628 exige informar qué datos personales se recolectan, con qué finalidad, por cuánto tiempo se conservan y cómo el cliente puede solicitar su eliminación.</p><p>Reemplaza este contenido desde el panel de administración, en Páginas.</p>',
            ],
            [
                'titulo'           => 'Despacho y Devoluciones',
                'slug'             => 'despacho-y-devoluciones',
                'bajada'           => 'Plazos, cobertura y cambio de envases',
                'meta_descripcion' => 'Plazos de despacho, comunas con cobertura y política de devolución de envases.',
                'contenido'        => '<p>Despachamos en 24 a 48 horas hábiles dentro de Santiago y la Región Metropolitana.</p><p>El despacho es sin costo sobre el monto mínimo indicado en la parte superior del sitio. Bajo ese monto se aplica la tarifa estándar.</p><p><strong>Envases retornables:</strong> al recibir una recarga debes entregar la misma cantidad de bidones vacíos y en buen estado. Si no los tienes, se cobra el valor del envase.</p>',
            ],
        ];

        foreach ($paginas as $pagina) {
            Page::updateOrCreate(['slug' => $pagina['slug']], $pagina + ['activo' => true]);
        }

        $enlaces = [
            // Menú principal — mismo orden que tenía el layout
            ['header', 'Inicio', 'ruta', 'home', 1],
            ['header', 'Productos', 'ruta', 'productos.index', 2],
            ['header', 'Empresas', 'ruta', 'empresas', 3],
            ['header', 'Ofertas', 'ruta', 'ofertas', 4],
            ['header', 'Nosotros', 'ruta', 'nosotros', 5],
            ['header', 'Contacto', 'ruta', 'contacto', 6],

            // Footer columna 1 — categorías del catálogo
            ['footer_1', 'Agua Purificada', 'categoria', 'agua-purificada', 1],
            ['footer_1', 'Dispensadores', 'categoria', 'dispensadores', 2],
            ['footer_1', 'Bombas', 'categoria', 'bombas', 3],
            ['footer_1', 'Accesorios', 'categoria', 'accesorios', 4],
            ['footer_1', 'Filtros', 'categoria', 'repuestos-y-filtros', 5],
            ['footer_1', 'Packs y Promos', 'categoria', 'packs-y-promos', 6],

            // Footer columna 2 — antes tenía tres enlaces muertos apuntando a "#"
            ['footer_2', 'Nosotros', 'ruta', 'nosotros', 1],
            ['footer_2', 'Términos y Condiciones', 'pagina', 'terminos-y-condiciones', 2],
            ['footer_2', 'Política de Privacidad', 'pagina', 'politica-de-privacidad', 3],
            ['footer_2', 'Despacho y Devoluciones', 'pagina', 'despacho-y-devoluciones', 4],
            ['footer_2', 'Mi Cuenta', 'ruta', 'login', 5],

            // Footer columna 3 — franja legal inferior
            ['footer_3', 'Términos y Condiciones', 'pagina', 'terminos-y-condiciones', 1],
            ['footer_3', 'Política de Privacidad', 'pagina', 'politica-de-privacidad', 2],
        ];

        foreach ($enlaces as [$ubicacion, $etiqueta, $tipo, $destino, $orden]) {
            MenuItem::updateOrCreate(
                ['ubicacion' => $ubicacion, 'etiqueta' => $etiqueta, 'orden' => $orden],
                ['tipo' => $tipo, 'destino' => $destino, 'activo' => true]
            );
        }

        MenuItem::olvidarCache();

        $this->command->info('NavegacionSeeder: '.count($enlaces).' enlaces y '.count($paginas).' páginas.');
    }
}
