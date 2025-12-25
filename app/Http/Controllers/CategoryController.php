<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display the specified category with its products.
     */
    public function show(Category $category)
    {
        $products = $category->products()
            ->active()
            ->ordered()
            ->paginate(8);

        return Inertia::render('Category', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}
