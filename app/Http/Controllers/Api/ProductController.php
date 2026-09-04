<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /** GET /api/products */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('category')->activo();

        // Filtro por categoría
        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        // Filtro por destacado
        if ($request->boolean('destacado')) {
            $query->destacado();
        }

        // Búsqueda por texto
        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('descripcion', 'like', "%{$term}%")
                  ->orWhere('sku', 'like', "%{$term}%");
            });
        }

        // Filtro precio
        if ($request->filled('precio_min')) {
            $query->where('precio', '>=', $request->precio_min);
        }
        if ($request->filled('precio_max')) {
            $query->where('precio', '<=', $request->precio_max);
        }

        // Ordenamiento
        $sortField = in_array($request->sort, ['precio', 'nombre', 'created_at', 'stock'])
            ? $request->sort : 'created_at';
        $sortDir = $request->direction === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortField, $sortDir);

        $products = $query->paginate($request->integer('per_page', 12));

        return response()->json($products);
    }

    /** GET /api/products/{slug} */
    public function show(string $slug): JsonResponse
    {
        $product = Product::with('category')
            ->activo()
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($product);
    }
}
