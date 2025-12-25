<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
    ];

    /**
     * Get the user that owns the cart.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the cart.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the total price of all items in the cart.
     */
    public function getTotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->price_at_time * $item->quantity;
        });
    }

    /**
     * Get the total number of items in the cart.
     */
    public function getItemsCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Get or create a cart for the user.
     */
    public static function getOrCreateForUser(int $userId): self
    {
        return static::firstOrCreate(['user_id' => $userId]);
    }
}