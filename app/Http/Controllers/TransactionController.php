<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class TransactionController extends Controller
{
    /**
     * Display a listing of the user's transactions.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $transactions = Transaction::where('user_id', $user->id)
            ->withCount('items')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Transactions', [
            'transactions' => $transactions,
        ]);
    }

    /**
     * Store a new transaction (checkout).
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $validator = Validator::make($request->all(), [
            'delivery_address' => 'required|string|max:255',
            'delivery_city' => 'required|string|max:255',
            'delivery_zip_code' => 'required|string|max:20',
            'selected_items' => 'required|array|min:1',
            'selected_items.*' => 'exists:cart_items,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $cart = Cart::getOrCreateForUser($user->id);
        $selectedItemIds = $request->input('selected_items');
        
        // Get selected cart items with products
        $cartItems = CartItem::whereIn('id', $selectedItemIds)
            ->where('cart_id', $cart->id)
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->withErrors(['selected_items' => 'No valid items selected.'])->withInput();
        }

        // Validate stock availability
        foreach ($cartItems as $cartItem) {
            if (!$cartItem->product) {
                return back()->withErrors(['selected_items' => 'One or more products are no longer available.'])->withInput();
            }

            if ($cartItem->product->stock_quantity < $cartItem->quantity) {
                return back()->withErrors([
                    'selected_items' => "Insufficient stock for {$cartItem->product->name}. Available: {$cartItem->product->stock_quantity}, Requested: {$cartItem->quantity}"
                ])->withInput();
            }
        }

        // Calculate totals
        $subtotal = $cartItems->sum('total_price');
        $deliveryFee = $subtotal >= 299 ? 0 : 25;
        $total = $subtotal + $deliveryFee;

        // Create transaction in a database transaction
        try {
            DB::beginTransaction();

            // Create transaction
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'delivery_address' => $request->input('delivery_address'),
                'delivery_city' => $request->input('delivery_city'),
                'delivery_zip_code' => $request->input('delivery_zip_code'),
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'status' => 'completed',
            ]);

            // Create transaction items and deduct stock
            foreach ($cartItems as $cartItem) {
                // Create transaction item with cached product data
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_image_url' => $cartItem->product->image_url,
                    'quantity' => $cartItem->quantity,
                    'price_at_time' => $cartItem->price_at_time,
                    'total_price' => $cartItem->total_price,
                ]);

                // Deduct stock
                $cartItem->product->reduceStock($cartItem->quantity);
            }

            // Remove cart items
            CartItem::whereIn('id', $selectedItemIds)->delete();

            DB::commit();

            $transaction->load(['items']);

            return redirect()->route('cart.index')
                ->with('success', 'Order placed successfully!')
                ->with('transaction', [
                    'id' => $transaction->id,
                    'transaction_number' => $transaction->transaction_number,
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors(['error' => 'Failed to process order. Please try again.'])->withInput();
        }
    }

    /**
     * Show the thank you page.
     */
    public function thankYou($transactionNumber)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('cart.index');
        }

        $transaction = Transaction::where('transaction_number', $transactionNumber)
            ->where('user_id', $user->id)
            ->with(['items'])
            ->first();

        if (!$transaction) {
            abort(404, 'Transaction not found');
        }

        return Inertia::render('ThankYou', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Show the transaction details page.
     */
    public function show($transactionNumber)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('cart.index');
        }

        $transaction = Transaction::where('transaction_number', $transactionNumber)
            ->where('user_id', $user->id)
            ->with(['items'])
            ->first();

        if (!$transaction) {
            abort(404, 'Transaction not found');
        }

        return Inertia::render('Transaction', [
            'transaction' => $transaction,
        ]);
    }
}
