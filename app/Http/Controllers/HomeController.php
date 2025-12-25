<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;
use Laravel\Fortify\Features;

class HomeController extends Controller
{
    /**
     * Display the home page with featured products.
     */
    public function index()
    {
        $featuredProducts = Product::query()
            ->active()
            ->featured()
            ->ordered()
            ->limit(4)
            ->get();

        return Inertia::render('Home', [
            'canRegister' => Features::enabled(Features::registration()),
            'featuredProducts' => $featuredProducts,
        ]);
    }
}

