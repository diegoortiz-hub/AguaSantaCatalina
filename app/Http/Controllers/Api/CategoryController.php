<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /** GET /api/categories */
    public function index(): JsonResponse
    {
        $categories = Category::activo()
            ->ordenado()
            ->withCount(['products' => fn ($q) => $q->activo()])
            ->get();

        return response()->json($categories);
    }
}
