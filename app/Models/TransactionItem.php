<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'product_name',
        'product_image_url',
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
        'price_at_time_formatted',
        'total_price_formatted',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($transactionItem) {
            // Calculate total_price before saving
            if ($transactionItem->isDirty(['quantity', 'price_at_time']) || !$transactionItem->exists) {
                $transactionItem->total_price = $transactionItem->price_at_time * $transactionItem->quantity;
            }
        });
    }

    /**
     * Get the transaction that owns the transaction item.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the product for the transaction item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get formatted price at time.
     */
    public function getPriceAtTimeFormattedAttribute(): string
    {
        return rtrim(rtrim(number_format($this->price_at_time, 8), '0'), '.');
    }

    /**
     * Get formatted total price.
     */
    public function getTotalPriceFormattedAttribute(): string
    {
        return rtrim(rtrim(number_format($this->total_price, 8), '0'), '.');
    }
}
