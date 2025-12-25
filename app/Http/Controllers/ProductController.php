<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        // Only show active products
        if (!$product->is_active) {
            abort(404);
        }

        // Load category relationship
        $product->load('category');

        // Static reviews data
        $reviews = [
            [
                'id' => 1,
                'user_name' => 'Sarah Johnson',
                'user_avatar' => 'SJ',
                'rating' => 5,
                'comment' => 'Absolutely love this product! The quality is exceptional and it looks exactly as described. Fast shipping and great packaging. Highly recommend!',
                'date' => '2024-01-15',
                'verified_purchase' => true,
            ],
            [
                'id' => 2,
                'user_name' => 'Michael Chen',
                'user_avatar' => 'MC',
                'rating' => 5,
                'comment' => 'Great value for money. The product arrived in perfect condition and assembly was straightforward. Very satisfied with my purchase.',
                'date' => '2024-01-10',
                'verified_purchase' => true,
            ],
            [
                'id' => 3,
                'user_name' => 'Emily Rodriguez',
                'user_avatar' => 'ER',
                'rating' => 4,
                'comment' => 'Good quality product overall. Minor issue with one part but customer service was very helpful in resolving it. Would buy again.',
                'date' => '2024-01-05',
                'verified_purchase' => true,
            ],
            [
                'id' => 4,
                'user_name' => 'David Thompson',
                'user_avatar' => 'DT',
                'rating' => 5,
                'comment' => 'Exceeded my expectations! The build quality is solid and it fits perfectly in my space. Delivery was prompt and the item was well-protected.',
                'date' => '2023-12-28',
                'verified_purchase' => true,
            ],
            [
                'id' => 5,
                'user_name' => 'Lisa Anderson',
                'user_avatar' => 'LA',
                'rating' => 4,
                'comment' => 'Nice product, good quality. The color matches the description. Only downside is it took a bit longer to assemble than expected.',
                'date' => '2023-12-20',
                'verified_purchase' => false,
            ],
        ];

        return Inertia::render('Product', [
            'product' => $product,
            'reviews' => $reviews,
        ]);
    }
}

