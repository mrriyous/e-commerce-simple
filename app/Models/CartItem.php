<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price_at_time',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_at_time' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    protected $appends = [
        'subtotal',
        'price_at_time_formatted',
        'total_price_formatted',
        'subtotal_formatted',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($cartItem) {
            // Calculate total_price before saving
            if ($cartItem->isDirty(['quantity', 'price_at_time']) || !$cartItem->exists) {
                $cartItem->total_price = $cartItem->price_at_time * $cartItem->quantity;
            }
        });
    }

    /**
     * Get the cart that owns the cart item.
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the product for the cart item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the subtotal for this cart item.
     */
    public function getSubtotalAttribute(): float
    {
        return $this->price_at_time * $this->quantity;
    }

    /**
     * Increment the quantity of this cart item.
     */
    public function incrementQuantity(int $amount = 1): bool
    {
        $this->quantity += $amount;
        return $this->save();
    }

    /**
     * Decrement the quantity of this cart item.
     */
    public function decrementQuantity(int $amount = 1): bool
    {
        if ($this->quantity > $amount) {
            $this->quantity -= $amount;
            return $this->save();
        }
        return false;
    }

    public function getPriceAtTimeFormattedAttribute(): string
    {
        return  rtrim(rtrim(number_format($this->price_at_time, 8), '0'), '.');
    }

    public function getTotalPriceFormattedAttribute(): string
    {
        return rtrim(rtrim(number_format($this->total_price, 8), '0'), '.');
    }
    
    public function getSubtotalFormattedAttribute(): string
    {
        return rtrim(rtrim(number_format($this->subtotal, 8), '0'), '.');
    }
}