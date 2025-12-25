<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'transaction_number',
        'delivery_address',
        'delivery_city',
        'delivery_zip_code',
        'subtotal',
        'delivery_fee',
        'total',
        'status',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_number)) {
                $transaction->transaction_number = static::generateTransactionNumber();
            }
        });
    }

    /**
     * Generate a unique transaction number.
     */
    protected static function generateTransactionNumber(): string
    {
        $lastTransaction = static::withTrashed()
            ->whereNotNull('transaction_number')
            ->where('transaction_number', 'like', 'TX-%')
            ->orderByRaw('CAST(SUBSTRING(transaction_number, 4) AS UNSIGNED) DESC')
            ->first();

        if ($lastTransaction && preg_match('/TX-(\d+)/', $lastTransaction->transaction_number, $matches)) {
            $sequence = (int) $matches[1] + 1;
        } else {
            $sequence = 1;
        }

        return 'TX-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'transaction_number';
    }

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the transaction.
     */
    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }
}
