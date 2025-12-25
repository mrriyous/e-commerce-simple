<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    /**
     * Display search results for products.
     */
    public function index(Request $request)
    {
        $query = trim($request->get('q', ''));

        $products = Product::query()
            ->active()
            ->when($query, function ($q) use ($query) {
                // Explode query into individual words
                $words = array_filter(explode(' ', $query), function ($word) {
                    return !empty(trim($word));
                });

                if (empty($words)) {
                    return;
                }

                $q->where(function ($queryBuilder) use ($words) {
                    foreach ($words as $word) {
                        $word = trim($word);
                        if (empty($word)) {
                            continue;
                        }
                        
                        $queryBuilder->where(function ($subQuery) use ($word) {
                            $subQuery
                                ->where('name', 'like', "%{$word}%")
                                ->orWhere('description', 'like', "%{$word}%")
                                ->orWhere('sku', 'like', "%{$word}%");
                        });
                    }
                });
            })
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Search', [
            'query' => $query,
            'products' => $products,
        ]);
    }
}

