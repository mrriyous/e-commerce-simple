<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('search', [SearchController::class, 'index'])->name('search');

Route::get('categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// Cart routes (requires authentication)
Route::middleware('auth')->group(function () {
    Route::get('cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('cart/items/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('cart/items/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    
    // Transaction routes (checkout)
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('transactions/{transactionNumber}/thank-you', [TransactionController::class, 'thankYou'])->name('transactions.thank-you');
    Route::get('transactions/{transactionNumber}', [TransactionController::class, 'show'])->name('transactions.show');
});

require __DIR__.'/settings.php';
