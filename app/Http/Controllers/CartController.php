<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Display the cart page.
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $cart = Cart::getOrCreateForUser($user->id);
        $cart->load(['items.product']);

        $transaction = session('transaction');

        return Inertia::render('Cart', [
            'cartData' => [
                'id' => $cart->id,
                'items' => $cart->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'quantity' => $item->quantity,
                        'price_at_time' => (string) $item->price_at_time,
                        'price_at_time_formatted' => $item->price_at_time_formatted,
                        'total_price' => (string) $item->total_price,
                        'total_price_formatted' => $item->total_price_formatted,
                        'subtotal_formatted' => $item->subtotal_formatted,
                        'product' => [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'slug' => $item->product->slug,
                            'price' => (string) $item->product->price,
                            'image_url' => $item->product->image_url,
                            'stock_quantity' => $item->product->stock_quantity,
                        ],
                    ];
                })->toArray(),
                'total' => (float) $cart->total,
                'items_count' => (int) $cart->items_count,
            ],
            'transaction' => $transaction,
        ]);
    }

    /**
     * Add a product to the cart.
     */
    public function add(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $user = Auth::user();
        
        if (!$user) {
            // Store the intended URL (product page) in session
            $product = Product::findOrFail($request->product_id);
            $intendedUrl = route('products.show', $product->slug);
            Session::put('intended', $intendedUrl);
            return redirect()->route('login');
        }

        $product = Product::findOrFail($request->product_id);

        if (!$product->is_active) {
            return redirect()->back()->withErrors(['message' => 'Sorry, this product is not available at the moment.']);
        }

        $quantity = $request->quantity ?? 1;

        if ($product->stock_quantity < $quantity) {
            return redirect()->back()->withErrors(['message' => 'Sorry, this product does not have enough stock available.']);
        }

        $cart = Cart::getOrCreateForUser($user->id);

        try {
            DB::transaction(function () use ($cart, $product, $quantity) {
                $cartItem = CartItem::firstOrNew([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                ]);

                if ($cartItem->exists) {
                    // Item already in cart, update quantity
                    $newQuantity = $cartItem->quantity + $quantity;
                    
                    if ($product->stock_quantity < $newQuantity) {
                        throw new \Exception('Sorry, this product does not have enough stock available.');
                    }
                    
                    $cartItem->quantity = $newQuantity;
                } else {
                    // New item, set initial values
                    $cartItem->quantity = $quantity;
                    $cartItem->price_at_time = $product->price;
                    $cartItem->total_price = $product->price * $quantity;
                }

                $cartItem->save();
            });

            return redirect()->back()->with('success', 'Item added to cart');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    /**
     * Update the quantity of a cart item.
     */
    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        
        if (!$user || $cartItem->cart->user_id !== $user->id) {
            return redirect()->route('login');
        }

        $product = $cartItem->product;

        if ($product->stock_quantity < $request->quantity) {
            return redirect()->back()->withErrors(['message' => 'Sorry, this product does not have enough stock available.']);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return redirect()->back()->with('success', 'Cart updated');
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(CartItem $cartItem): RedirectResponse
    {
        $user = Auth::user();
        
        if (!$user || $cartItem->cart->user_id !== $user->id) {
            return redirect()->route('login');
        }

        $cartItem->delete();

        return redirect()->back()->with('success', 'Item removed from cart');
    }

    /**
     * Clear all items from the cart.
     */
    public function clear(): RedirectResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $cart = Cart::where('user_id', $user->id)->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return redirect()->back()->with('success', 'Cart cleared');
    }
}