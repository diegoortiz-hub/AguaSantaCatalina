<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\ShopController;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\AccountController;

/*
|--------------------------------------------------------------------------
| Web Routes — Aguas Santa Catalina
|--------------------------------------------------------------------------
*/

// ── Tienda pública ────────────────────────────────────────────────────────
Route::get('/', [ShopController::class, 'home'])->name('home');
Route::get('/productos', [ShopController::class, 'catalog'])->name('productos.index');
Route::get('/productos/{slug}', [ShopController::class, 'product'])->name('productos.show');
Route::get('/carrito', [ShopController::class, 'cart'])->name('carrito');
Route::get('/checkout', [ShopController::class, 'checkout'])->name('checkout');
Route::get('/pedido/{id}/confirmacion', [ShopController::class, 'confirmation'])->name('pedido.confirmacion');

// ── Páginas informativas ──────────────────────────────────────────────────
Route::get('/empresas', [ShopController::class, 'empresas'])->name('empresas');
Route::get('/nosotros', [ShopController::class, 'nosotros'])->name('nosotros');
Route::get('/contacto', [ShopController::class, 'contacto'])->name('contacto');
Route::get('/ofertas', [ShopController::class, 'ofertas'])->name('ofertas');

// ── Autenticación ─────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Área de cliente (requiere auth) ───────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/mi-cuenta', [AccountController::class, 'orders'])->name('mi-cuenta');
    Route::get('/mi-cuenta/pedidos', [AccountController::class, 'orders'])->name('account.orders');
    Route::get('/mi-cuenta/perfil', [AccountController::class, 'profile'])->name('account.profile');
});

// ── Panel admin (requiere auth + rol admin) ───────────────────────────────
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/',                                  [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Productos
    Route::get('/productos',                         [AdminController::class, 'productosIndex'])->name('admin.productos.index');
    Route::get('/productos/crear',                   [AdminController::class, 'productosCreate'])->name('admin.productos.create');
    Route::post('/productos',                        [AdminController::class, 'productosStore'])->name('admin.productos.store');
    Route::get('/productos/{product}/editar',        [AdminController::class, 'productosEdit'])->name('admin.productos.edit');
    Route::put('/productos/{product}',               [AdminController::class, 'productosUpdate'])->name('admin.productos.update');
    Route::delete('/productos/{product}',            [AdminController::class, 'productosDestroy'])->name('admin.productos.destroy');

    // Pedidos
    Route::get('/pedidos',                           [AdminController::class, 'pedidosIndex'])->name('admin.pedidos.index');
    Route::get('/pedidos/{order}',                   [AdminController::class, 'pedidosShow'])->name('admin.pedidos.show');
    Route::patch('/pedidos/{order}/estado',          [AdminController::class, 'pedidosUpdateEstado'])->name('admin.pedidos.estado');

    // Clientes
    Route::get('/clientes',                          [AdminController::class, 'clientesIndex'])->name('admin.clientes.index');

    // Banners
    Route::get('/banners',                           [AdminController::class, 'bannersIndex'])->name('admin.banners.index');
    Route::post('/banners',                          [AdminController::class, 'bannersStore'])->name('admin.banners.store');
    Route::patch('/banners/{banner}/toggle',         [AdminController::class, 'bannersToggle'])->name('admin.banners.toggle');
    Route::delete('/banners/{banner}',               [AdminController::class, 'bannersDestroy'])->name('admin.banners.destroy');

    // Cupones
    Route::get('/cupones',                           [AdminController::class, 'cuponesIndex'])->name('admin.cupones.index');
    Route::post('/cupones',                          [AdminController::class, 'cuponesStore'])->name('admin.cupones.store');
    Route::patch('/cupones/{coupon}/toggle',         [AdminController::class, 'cuponesToggle'])->name('admin.cupones.toggle');
    Route::delete('/cupones/{coupon}',               [AdminController::class, 'cuponesDestroy'])->name('admin.cupones.destroy');

    // Estadísticas
    Route::get('/estadisticas',                      [AdminController::class, 'estadisticas'])->name('admin.estadisticas');

    // Configuración
    Route::get('/configuracion',                     [AdminController::class, 'configuracion'])->name('admin.configuracion');
    Route::post('/configuracion',                    [AdminController::class, 'configuracionSave'])->name('admin.configuracion.save');
});
