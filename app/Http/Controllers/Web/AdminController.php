<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Models\User;
use App\Support\Settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminController extends Controller
{
    // ── Dashboard ──────────────────────────────────────────────────────────

    /** Estados que cuentan como venta efectiva. */
    private const ESTADOS_VENTA = ['confirmado', 'enviado', 'entregado'];

    public function dashboard(): View
    {
        $inicioHoy  = today();
        $inicioAyer = today()->subDay();
        $inicioMes  = now()->startOfMonth();
        $inicioMesAnterior = now()->subMonthNoOverflow()->startOfMonth();

        // Rangos en vez de whereDate/whereMonth: envolver created_at en una función
        // impide que el motor aproveche un índice sobre la columna.
        $ventas_hoy  = Order::whereIn('estado', self::ESTADOS_VENTA)
            ->whereBetween('created_at', [$inicioHoy, $inicioHoy->copy()->endOfDay()])->sum('total');
        $ventas_ayer = Order::whereIn('estado', self::ESTADOS_VENTA)
            ->whereBetween('created_at', [$inicioAyer, $inicioAyer->copy()->endOfDay()])->sum('total');
        $ventas_mes  = Order::whereIn('estado', self::ESTADOS_VENTA)
            ->where('created_at', '>=', $inicioMes)->sum('total');

        $pedidos_pendientes = Order::where('estado', 'pendiente')->count();
        // Excluye cancelados para que cuadre con el criterio de ventas_mes.
        $pedidos_mes = Order::where('estado', '!=', 'cancelado')
            ->where('created_at', '>=', $inicioMes)->count();

        $clientes_mes = User::where('rol', 'cliente')
            ->where('created_at', '>=', $inicioMes)->count();
        $clientes_mes_ant = User::where('rol', 'cliente')
            ->whereBetween('created_at', [$inicioMesAnterior, $inicioMes])->count();

        $stock_bajo = Product::whereColumn('stock', '<=', 'stock_minimo')->activo()->count();

        $var_ventas   = $ventas_ayer > 0 ? round((($ventas_hoy - $ventas_ayer) / $ventas_ayer) * 100) : 0;
        $var_clientes = $clientes_mes_ant > 0 ? round((($clientes_mes - $clientes_mes_ant) / $clientes_mes_ant) * 100) : 0;

        $stats = compact('ventas_hoy','ventas_mes','ventas_ayer','pedidos_pendientes','pedidos_mes','clientes_mes','clientes_mes_ant','stock_bajo','var_ventas','var_clientes');

        // ── Series reales para los gráficos ──────────────────────────────
        $desde30 = now()->subDays(29)->startOfDay();

        $serieVentas = $this->rellenarDias(
            Order::selectRaw('DATE(created_at) as fecha, SUM(total) as valor')
                ->whereIn('estado', self::ESTADOS_VENTA)
                ->where('created_at', '>=', $desde30)
                ->groupBy('fecha')->pluck('valor', 'fecha')->toArray(),
            30
        );

        $seriePedidos = $this->rellenarDias(
            Order::selectRaw('DATE(created_at) as fecha, COUNT(*) as valor')
                ->where('estado', '!=', 'cancelado')
                ->where('created_at', '>=', $desde30)
                ->groupBy('fecha')->pluck('valor', 'fecha')->toArray(),
            30
        );

        $serieClientes = $this->rellenarDias(
            User::selectRaw('DATE(created_at) as fecha, COUNT(*) as valor')
                ->where('rol', 'cliente')
                ->where('created_at', '>=', $desde30)
                ->groupBy('fecha')->pluck('valor', 'fecha')->toArray(),
            30
        );

        // Serie de 12 meses. Se agrupa por día (DATE() existe en MySQL y en SQLite,
        // a diferencia de DATE_FORMAT) y el total mensual se arma en PHP.
        $porDia = Order::selectRaw('DATE(created_at) as fecha, SUM(total) as valor')
            ->whereIn('estado', self::ESTADOS_VENTA)
            ->where('created_at', '>=', now()->subMonthsNoOverflow(11)->startOfMonth())
            ->groupBy('fecha')->pluck('valor', 'fecha');

        $porMes = $porDia->reduce(function (array $acc, $valor, $fecha) {
            $mes = substr((string) $fecha, 0, 7);
            $acc[$mes] = ($acc[$mes] ?? 0) + (float) $valor;

            return $acc;
        }, []);

        $serieAnual = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->subMonthsNoOverflow($i)->format('Y-m');
            $serieAnual[] = (float) ($porMes[$m] ?? 0);
        }

        // Distribución real de medios de pago del mes
        $mediosPago = Order::selectRaw('metodo_pago, COUNT(*) as pedidos, SUM(total) as monto')
            ->where('estado', '!=', 'cancelado')
            ->where('created_at', '>=', $inicioMes)
            ->groupBy('metodo_pago')
            ->orderByDesc('pedidos')
            ->get();

        $pedidos_recientes    = Order::with(['items'])->latest()->limit(8)->get();
        $productos_stock_bajo = Product::with('category')->whereColumn('stock', '<=', 'stock_minimo')->activo()->get();

        return view('admin.dashboard', compact(
            'stats', 'pedidos_recientes', 'productos_stock_bajo',
            'serieVentas', 'seriePedidos', 'serieClientes', 'serieAnual', 'mediosPago'
        ));
    }

    /** Completa con ceros los días sin registros para que la serie no tenga huecos. */
    private function rellenarDias(array $datos, int $dias): array
    {
        $serie = [];
        for ($i = $dias - 1; $i >= 0; $i--) {
            $serie[] = (float) ($datos[now()->subDays($i)->format('Y-m-d')] ?? 0);
        }

        return $serie;
    }

    // ── Productos ──────────────────────────────────────────────────────────

    public function productosIndex(Request $request): View
    {
        $query = Product::with('category');

        if ($request->filled('q')) {
            $query->where('nombre', 'like', '%'.$request->q.'%');
        }
        if ($request->filled('categoria')) {
            $query->where('category_id', $request->categoria);
        }
        if ($request->filled('estado')) {
            $query->where('activo', $request->estado === 'activo');
        }

        $productos  = $query->latest()->paginate(20)->withQueryString();
        $categories = Category::ordenado()->get();

        return view('admin.productos.index', compact('productos', 'categories'));
    }

    public function productosCreate(): View
    {
        $categories = Category::ordenado()->get();
        $product    = new Product();
        return view('admin.productos.form', compact('product', 'categories'));
    }

    public function productosStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre'          => 'required|string|max:191',
            'category_id'     => 'required|exists:categories,id',
            'precio'          => 'required|numeric|min:0',
            'precio_original' => 'nullable|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'stock_minimo'    => 'required|integer|min:0',
            'sku'             => 'nullable|string|max:60',
            'descripcion'     => 'nullable|string',
            'badge'           => 'nullable|string|max:60',
            'badge_color'     => 'nullable|string|max:30',
            'destacado'       => 'boolean',
            'activo'          => 'boolean',
            'imagen'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $data['slug']      = Str::slug($data['nombre']).'-'.Str::random(4);
        $data['destacado'] = $request->boolean('destacado');
        $data['activo']    = $request->boolean('activo');

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        } else {
            unset($data['imagen']);
        }

        Product::create($data);

        return redirect()->route('admin.productos.index')->with('success', 'Producto creado exitosamente.');
    }

    public function productosEdit(Product $product): View
    {
        $categories = Category::ordenado()->get();
        return view('admin.productos.form', compact('product', 'categories'));
    }

    public function productosUpdate(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'nombre'          => 'required|string|max:191',
            'category_id'     => 'required|exists:categories,id',
            'precio'          => 'required|numeric|min:0',
            'precio_original' => 'nullable|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'stock_minimo'    => 'required|integer|min:0',
            'sku'             => 'nullable|string|max:60',
            'descripcion'     => 'nullable|string',
            'badge'           => 'nullable|string|max:60',
            'badge_color'     => 'nullable|string|max:30',
            'destacado'       => 'boolean',
            'activo'          => 'boolean',
            'imagen'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $data['destacado'] = $request->boolean('destacado');
        $data['activo']    = $request->boolean('activo');

        if ($request->hasFile('imagen')) {
            // Borrar imagen anterior si existe y es local
            if ($product->imagen && !str_starts_with($product->imagen, 'http')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        } else {
            unset($data['imagen']);
        }

        $product->update($data);

        return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado.');
    }

    public function productosDestroy(Product $product): RedirectResponse
    {
        $product->update(['activo' => false]);
        return redirect()->route('admin.productos.index')->with('success', 'Producto desactivado.');
    }

    // ── Pedidos ────────────────────────────────────────────────────────────

    public function pedidosIndex(Request $request): View
    {
        $query = Order::with('user');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre_cliente', 'like', '%'.$request->q.'%')
                  ->orWhere('email_cliente', 'like', '%'.$request->q.'%')
                  ->orWhere('id', $request->q);
            });
        }

        $pedidos = $query->latest()->paginate(25)->withQueryString();

        $conteos = [
            'todos'      => Order::count(),
            'pendiente'  => Order::where('estado', 'pendiente')->count(),
            'confirmado' => Order::where('estado', 'confirmado')->count(),
            'enviado'    => Order::where('estado', 'enviado')->count(),
            'entregado'  => Order::where('estado', 'entregado')->count(),
            'cancelado'  => Order::where('estado', 'cancelado')->count(),
        ];

        return view('admin.pedidos.index', compact('pedidos', 'conteos'));
    }

    public function pedidosShow(Order $order): View
    {
        $order->load(['items.product', 'user']);
        return view('admin.pedidos.show', compact('order'));
    }

    public function pedidosUpdateEstado(Request $request, Order $order): RedirectResponse
    {
        $request->validate(['estado' => 'required|in:pendiente,confirmado,enviado,entregado,cancelado']);
        $order->update(['estado' => $request->estado]);
        return back()->with('success', 'Estado actualizado a '.$order->fresh()->estadoLabel().'.');
    }

    // ── Clientes ───────────────────────────────────────────────────────────

    public function clientesIndex(Request $request): View
    {
        $query = User::where('rol', 'cliente')->withCount('orders')->withSum('orders', 'total');
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%'.$request->q.'%')
                  ->orWhere('email', 'like', '%'.$request->q.'%');
            });
        }
        $clientes     = $query->latest()->paginate(25)->withQueryString();
        $total        = User::where('rol', 'cliente')->count();
        $mes          = User::where('rol', 'cliente')->whereMonth('created_at', now()->month)->count();
        $volumenTotal = User::where('rol', 'cliente')->join('orders', 'users.id', '=', 'orders.user_id')->whereIn('orders.estado', ['confirmado','enviado','entregado'])->sum('orders.total');
        $gasto_promedio = $total > 0 ? round($volumenTotal / $total) : 0;
        return view('admin.clientes.index', compact('clientes', 'total', 'mes', 'volumenTotal', 'gasto_promedio'));
    }

    // ── Banners ────────────────────────────────────────────────────────────

    public function bannersIndex(): View
    {
        $banners = Banner::orderBy('orden')->orderBy('created_at', 'desc')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function bannersStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'titulo'    => 'required|string|max:191',
            'subtitulo' => 'nullable|string|max:255',
            'imagen'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'link'      => 'nullable|string|max:500',
            'orden'     => 'nullable|integer',
            'activo'    => 'boolean',
        ]);
        $data['activo'] = $request->boolean('activo');
        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('banners', 'public');
        } else {
            unset($data['imagen']);
        }
        Banner::create($data);
        return redirect()->route('admin.banners.index')->with('success', 'Banner creado.');
    }

    public function bannersToggle(Banner $banner): RedirectResponse
    {
        $banner->update(['activo' => ! $banner->activo]);
        return back()->with('success', 'Banner '.($banner->activo ? 'activado' : 'desactivado').'.');
    }

    public function bannersDestroy(Banner $banner): RedirectResponse
    {
        $banner->delete();
        return back()->with('success', 'Banner eliminado.');
    }

    // ── Cupones ────────────────────────────────────────────────────────────

    public function cuponesIndex(Request $request): View
    {
        $query = Coupon::query();
        if ($request->filled('q')) {
            $query->where('codigo', 'like', '%'.$request->q.'%');
        }
        $cupones = $query->latest()->paginate(25)->withQueryString();
        return view('admin.cupones.index', compact('cupones'));
    }

    public function cuponesStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'codigo'        => 'required|string|max:60|unique:coupons,codigo',
            'tipo'          => 'required|in:porcentaje,monto',
            'descuento'     => 'required|numeric|min:0',
            'minimo_compra' => 'nullable|numeric|min:0',
            'maximo_usos'   => 'nullable|integer|min:1',
            'vence_en'      => 'nullable|date',
            'activo'        => 'boolean',
        ]);
        $data['activo']        = $request->boolean('activo');
        $data['minimo_compra'] = $data['minimo_compra'] ?? 0;
        Coupon::create($data);
        return redirect()->route('admin.cupones.index')->with('success', 'Cupón creado.');
    }

    public function cuponesToggle(Coupon $coupon): RedirectResponse
    {
        $coupon->update(['activo' => ! $coupon->activo]);
        return back()->with('success', 'Cupón '.($coupon->activo ? 'activado' : 'pausado').'.');
    }

    public function cuponesDestroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();
        return back()->with('success', 'Cupón eliminado.');
    }

    // ── Estadísticas ───────────────────────────────────────────────────────

    public function estadisticas(): View
    {
        $estados = ['confirmado', 'enviado', 'entregado'];
        $año     = now()->year;

        // KPIs del mes actual
        $ventasMes    = Order::whereMonth('created_at', now()->month)->whereYear('created_at', $año)->whereIn('estado', $estados)->sum('total');
        $ventasMesAnt = Order::whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', $año)->whereIn('estado', $estados)->sum('total');
        $pedidosMes   = Order::whereMonth('created_at', now()->month)->whereYear('created_at', $año)->whereIn('estado', $estados)->count();
        $ticketProm   = $pedidosMes > 0 ? round($ventasMes / $pedidosMes) : 0;
        $varVentas    = $ventasMesAnt > 0 ? round((($ventasMes - $ventasMesAnt) / $ventasMesAnt) * 100, 1) : 0;

        // Evolución mensual (últimos 6 meses)
        $mensual = [];
        for ($i = 5; $i >= 0; $i--) {
            $dt = now()->subMonths($i);
            $mensual[] = [
                'mes'     => $dt->locale('es')->isoFormat('MMM'),
                'ventas'  => (int) Order::whereMonth('created_at', $dt->month)->whereYear('created_at', $dt->year)->whereIn('estado', $estados)->sum('total'),
                'pedidos' => Order::whereMonth('created_at', $dt->month)->whereYear('created_at', $dt->year)->count(),
            ];
        }
        $ventasArr  = array_column($mensual, 'ventas');
        $maxMensual = count($ventasArr) > 0 ? max(max($ventasArr), 1) : 1;

        // Top productos vendidos
        $topProductos = \App\Models\OrderItem::with('product')
            ->selectRaw('product_id, nombre_producto, SUM(cantidad) as total_unidades, SUM(precio_unitario * cantidad) as total_revenue')
            ->groupBy('product_id', 'nombre_producto')
            ->orderByDesc('total_revenue')
            ->limit(4)
            ->get();
        $maxRevenue = $topProductos->max('total_revenue') ?: 1;

        // Pedidos por estado (para distribución)
        $porEstado = Order::selectRaw('estado, COUNT(*) as total')->groupBy('estado')->pluck('total', 'estado');

        // Métodos de pago
        $porMetodo = Order::whereIn('estado', $estados)
            ->selectRaw('metodo_pago, SUM(total) as total')
            ->groupBy('metodo_pago')
            ->pluck('total', 'metodo_pago');
        $totalPagos = $porMetodo->sum() ?: 1;

        return view('admin.estadisticas', compact(
            'ventasMes', 'ventasMesAnt', 'varVentas', 'pedidosMes', 'ticketProm',
            'mensual', 'maxMensual', 'topProductos', 'maxRevenue',
            'porEstado', 'porMetodo', 'totalPagos'
        ));
    }

    // ── Configuración ──────────────────────────────────────────────────────

    // ── Páginas editables ──────────────────────────────────────────────────

    public function paginasIndex(): View
    {
        $paginas = Page::orderBy('titulo')->get();

        return view('admin.paginas.index', compact('paginas'));
    }

    public function paginasCreate(): View
    {
        return view('admin.paginas.form', ['page' => new Page(['activo' => true])]);
    }

    public function paginasStore(Request $request): RedirectResponse
    {
        Page::create($this->validarPagina($request));
        MenuItem::olvidarCache();

        return redirect()->route('admin.paginas.index')->with('success', 'Página creada.');
    }

    public function paginasEdit(Page $page): View
    {
        return view('admin.paginas.form', compact('page'));
    }

    public function paginasUpdate(Request $request, Page $page): RedirectResponse
    {
        $page->update($this->validarPagina($request, $page));
        MenuItem::olvidarCache();

        return redirect()->route('admin.paginas.index')->with('success', 'Página actualizada.');
    }

    public function paginasDestroy(Page $page): RedirectResponse
    {
        $enlaces = MenuItem::where('tipo', 'pagina')->where('destino', $page->slug)->count();
        $page->delete();
        MenuItem::olvidarCache();

        $aviso = $enlaces > 0
            ? "Página eliminada. Había {$enlaces} enlace(s) apuntando a ella: dejaron de mostrarse en el sitio."
            : 'Página eliminada.';

        return redirect()->route('admin.paginas.index')->with('success', $aviso);
    }

    private function validarPagina(Request $request, ?Page $page = null): array
    {
        $data = $request->validate([
            'titulo'           => ['required', 'string', 'max:191'],
            'slug'             => ['nullable', 'string', 'max:191', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'bajada'           => ['nullable', 'string', 'max:255'],
            'contenido'        => ['nullable', 'string'],
            'meta_descripcion' => ['nullable', 'string', 'max:300'],
        ], [
            'slug.regex' => 'La dirección sólo admite minúsculas, números y guiones.',
        ]);

        $data['activo'] = $request->boolean('activo');

        // El slug se normaliza en el modelo; aquí sólo se comprueba que no choque.
        $slug = Str::slug($data['slug'] ?: $data['titulo']);

        $choca = Page::where('slug', $slug)
            ->when($page, fn ($q) => $q->whereKeyNot($page->getKey()))
            ->exists();

        if ($choca) {
            throw ValidationException::withMessages([
                'slug' => "Ya existe una página en /{$slug}.",
            ]);
        }

        // Una página no puede ocupar una dirección que ya usa la tienda.
        if (in_array($slug, self::SLUGS_RESERVADOS, true)) {
            throw ValidationException::withMessages([
                'slug' => "/{$slug} es una dirección del sistema. Elige otra.",
            ]);
        }

        return $data;
    }

    /** Direcciones que ya pertenecen a la tienda y no puede tomar una página. */
    private const SLUGS_RESERVADOS = [
        'admin', 'productos', 'carrito', 'checkout', 'ofertas', 'empresas',
        'nosotros', 'contacto', 'login', 'logout', 'register', 'mi-cuenta',
        'pedido', 'olvide-mi-clave', 'restablecer-clave', 'storage', 'up', 'api',
    ];

    // ── Menús del sitio ────────────────────────────────────────────────────

    public function menusIndex(): View
    {
        $menus = MenuItem::orderBy('orden')->orderBy('id')->get()->groupBy('ubicacion');
        $paginas    = Page::orderBy('titulo')->get(['titulo', 'slug']);
        $categorias = Category::activo()->ordenado()->get(['nombre', 'slug']);

        return view('admin.menus.index', compact('menus', 'paginas', 'categorias'));
    }

    public function menusStore(Request $request): RedirectResponse
    {
        MenuItem::create($this->validarEnlace($request));

        return back()->with('success', 'Enlace agregado al menú.');
    }

    public function menusUpdate(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $menuItem->update($this->validarEnlace($request));

        return back()->with('success', 'Enlace actualizado.');
    }

    public function menusToggle(MenuItem $menuItem): RedirectResponse
    {
        $menuItem->update(['activo' => ! $menuItem->activo]);

        return back()->with('success', $menuItem->activo ? 'Enlace visible.' : 'Enlace oculto.');
    }

    public function menusDestroy(MenuItem $menuItem): RedirectResponse
    {
        $menuItem->delete();

        return back()->with('success', 'Enlace eliminado.');
    }

    private function validarEnlace(Request $request): array
    {
        $data = $request->validate([
            'ubicacion' => ['required', Rule::in(array_keys(MenuItem::UBICACIONES))],
            'etiqueta'  => ['required', 'string', 'max:60'],
            'tipo'      => ['required', Rule::in(['ruta', 'categoria', 'pagina', 'url'])],
            'destino'   => ['required', 'string', 'max:255'],
            'orden'     => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        $data['orden']         = $data['orden'] ?? 0;
        $data['activo']        = $request->boolean('activo', true);
        $data['nueva_pestana'] = $request->boolean('nueva_pestana');

        // El destino se valida contra lo que existe, según el tipo: así un enlace
        // no puede apuntar a una ruta de admin ni a una página borrada.
        $valido = match ($data['tipo']) {
            'ruta'      => array_key_exists($data['destino'], MenuItem::RUTAS_PERMITIDAS),
            'categoria' => Category::where('slug', $data['destino'])->exists(),
            'pagina'    => Page::where('slug', $data['destino'])->exists(),
            'url'       => (bool) filter_var($data['destino'], FILTER_VALIDATE_URL)
                            || str_starts_with($data['destino'], '/'),
        };

        if (! $valido) {
            throw ValidationException::withMessages([
                'destino' => 'El destino no existe o no está permitido para ese tipo de enlace.',
            ]);
        }

        return $data;
    }

    public function configuracion(Settings $ajustes): View
    {
        $settings = $ajustes->all();
        return view('admin.configuracion', compact('settings'));
    }

    public function configuracionSave(Request $request, Settings $ajustes): RedirectResponse
    {
        $valores = $request->only([
            'empresa','rut','email','telefono','direccion','comuna','ciudad','horario',
            'banco','tipo_cuenta','nro_cuenta','titular','rut_titular','email_pagos',
            'whatsapp','despacho_gratis','despacho_estandar','despacho_express',
            'footer_descripcion','footer_1_titulo','footer_2_titulo',
            'red_facebook','red_instagram',
            'mantencion_mensaje','mantencion_fin',
        ]);

        foreach (['despacho_gratis', 'despacho_estandar', 'despacho_express'] as $campo) {
            if (isset($valores[$campo])) {
                $valores[$campo] = (int) $valores[$campo];
            }
        }

        $valores['mantencion_activa'] = $request->boolean('mantencion_activa');

        $ajustes->save($valores);

        return back()->with('success', 'Configuración guardada correctamente.');
    }
}
