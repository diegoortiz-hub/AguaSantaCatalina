<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function home(): View
    {
        $banners    = Banner::activo()->get();
        $destacados = Product::with('category')->activo()->destacado()->conStock()->limit(8)->get();
        $categories = Category::activo()->ordenado()->get();

        return view('home', compact('banners', 'destacados', 'categories'));
    }

    public function catalog(Request $request): View
    {
        $query = Product::with('category')->activo();

        if ($request->filled('categoria')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->categoria));
        }
        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(fn ($q) => $q
                ->where('nombre', 'like', "%{$term}%")
                ->orWhere('descripcion', 'like', "%{$term}%")
            );
        }
        if ($request->filled('precio_min')) {
            $query->where('precio', '>=', (int) $request->precio_min);
        }
        if ($request->filled('precio_max')) {
            $query->where('precio', '<=', (int) $request->precio_max);
        }
        if ($request->boolean('solo_stock')) {
            $query->conStock();
        }
        if ($request->boolean('destacado')) {
            $query->destacado();
        }

        match ($request->input('sort', 'newest')) {
            'precio_asc'  => $query->orderBy('precio'),
            'precio_desc' => $query->orderByDesc('precio'),
            'nombre'      => $query->orderBy('nombre'),
            'destacado'   => $query->orderByDesc('destacado')->orderBy('nombre'),
            default       => $query->orderByDesc('created_at'),
        };

        $products        = $query->paginate(12)->withQueryString();
        $categories      = Category::activo()->ordenado()->withCount(['products' => fn ($q) => $q->activo()])->get();
        $currentCategory = $request->filled('categoria')
            ? $categories->firstWhere('slug', $request->categoria)
            : null;
        $maxPriceInDb    = Product::activo()->max('precio') ?? 150000;

        return view('catalog', compact('products', 'categories', 'currentCategory', 'maxPriceInDb'));
    }

    public function product(string $slug): View
    {
        $product  = Product::with('category')->activo()->where('slug', $slug)->firstOrFail();
        $related  = Product::with('category')
            ->activo()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('product', compact('product', 'related'));
    }

    public function cart(): View
    {
        return view('cart');
    }

    public function checkout(): View
    {
        return view('checkout');
    }

    public function confirmation(int $id): View
    {
        $order = Order::with('items.product')->findOrFail($id);

        return view('confirmation', compact('order'));
    }

    public function empresas(): View
    {
        return view('empresas');
    }

    public function nosotros(): View
    {
        return view('nosotros');
    }

    public function contacto(): View
    {
        return view('contacto');
    }

    public function ofertas(): View
    {
        $products = Product::with('category')
            ->activo()
            ->conStock()
            ->whereNotNull('precio_original')
            ->whereRaw('precio < precio_original')
            ->orderByRaw('(precio_original - precio) DESC')
            ->get();

        return view('ofertas', compact('products'));
    }
}
