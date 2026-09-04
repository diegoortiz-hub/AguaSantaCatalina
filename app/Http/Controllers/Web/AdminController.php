<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminController extends Controller
{
    // ── Dashboard ──────────────────────────────────────────────────────────

    public function dashboard(): View
    {
        $hoy       = today();
        $mes       = now()->month;
        $año       = now()->year;
        $ayer      = today()->subDay();

        // Stats principales
        $ventas_hoy   = Order::whereDate('created_at', $hoy)->whereIn('estado', ['confirmado','enviado','entregado'])->sum('total');
        $ventas_ayer  = Order::whereDate('created_at', $ayer)->whereIn('estado', ['confirmado','enviado','entregado'])->sum('total');
        $ventas_mes   = Order::whereMonth('created_at', $mes)->whereYear('created_at', $año)->whereIn('estado', ['confirmado','enviado','entregado'])->sum('total');

        $pedidos_pendientes = Order::where('estado', 'pendiente')->count();
        $pedidos_mes        = Order::whereMonth('created_at', $mes)->whereYear('created_at', $año)->count();

        $clientes_mes       = User::where('rol', 'cliente')->whereMonth('created_at', $mes)->whereYear('created_at', $año)->count();
        $clientes_mes_ant   = User::where('rol', 'cliente')->whereMonth('created_at', now()->subMonth()->month)->count();

        $stock_bajo         = Product::whereColumn('stock', '<=', 'stock_minimo')->activo()->count();

        // Variaciones porcentuales
        $var_ventas   = $ventas_ayer > 0 ? round((($ventas_hoy - $ventas_ayer) / $ventas_ayer) * 100) : 0;
        $var_clientes = $clientes_mes_ant > 0 ? round((($clientes_mes - $clientes_mes_ant) / $clientes_mes_ant) * 100) : 0;

        $stats = compact('ventas_hoy','ventas_mes','ventas_ayer','pedidos_pendientes','pedidos_mes','clientes_mes','clientes_mes_ant','stock_bajo','var_ventas','var_clientes');

        // Gráfico 30 días — ventas diarias
        $ventasDiarias = Order::selectRaw('DATE(created_at) as fecha, SUM(total) as total')
            ->whereIn('estado', ['confirmado','enviado','entregado'])
            ->whereBetween('created_at', [now()->subDays(29)->startOfDay(), now()->endOfDay()])
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->pluck('total', 'fecha')
            ->toArray();

        $chartLabels = [];
        $chartData   = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('d');
            $chartData[]   = $ventasDiarias[$d] ?? 0;
        }

        $pedidos_recientes    = Order::with(['items'])->latest()->limit(8)->get();
        $productos_stock_bajo = Product::with('category')->whereColumn('stock', '<=', 'stock_minimo')->activo()->get();

        return view('admin.dashboard', compact(
            'stats', 'pedidos_recientes', 'productos_stock_bajo',
            'chartLabels', 'chartData'
        ));
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

    private function settingsPath(): string
    {
        return storage_path('app/settings.json');
    }

    private function loadSettings(): array
    {
        if (file_exists($this->settingsPath())) {
            return json_decode(file_get_contents($this->settingsPath()), true) ?? [];
        }
        return [
            'empresa'        => 'Aguas Purificadas Santa Catalina',
            'rut'            => '76.000.000-0',
            'email'          => 'contacto@aguassantacatalina.cl',
            'telefono'       => '+56 9 9149 3272',
            'direccion'      => 'Av. Purificación 1234',
            'comuna'         => 'Santiago',
            'ciudad'         => 'Santiago',
            'horario'        => 'Lun–Sáb 08:00–18:00',
            'banco'          => 'Banco Estado',
            'tipo_cuenta'    => 'Cuenta Corriente',
            'nro_cuenta'     => '000000000',
            'titular'        => 'Aguas Purificadas Santa Catalina',
            'rut_titular'    => '76.000.000-0',
            'email_pagos'    => 'pagos@aguassantacatalina.cl',
            'whatsapp'       => '+56991493272',
            'despacho_gratis'=> 30000,
        ];
    }

    public function configuracion(): View
    {
        $settings = $this->loadSettings();
        return view('admin.configuracion', compact('settings'));
    }

    public function configuracionSave(Request $request): RedirectResponse
    {
        $settings = array_merge($this->loadSettings(), $request->only([
            'empresa','rut','email','telefono','direccion','comuna','ciudad','horario',
            'banco','tipo_cuenta','nro_cuenta','titular','rut_titular','email_pagos',
            'whatsapp','despacho_gratis',
            'mantencion_mensaje','mantencion_fin',
        ]));

        $settings['mantencion_activa'] = $request->boolean('mantencion_activa');

        file_put_contents($this->settingsPath(), json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return back()->with('success', 'Configuración guardada correctamente.');
    }
}
