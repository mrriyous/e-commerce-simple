<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionReportNotifProcess extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'report_date',
        'report_data',
        'total_sales',
        'total_items',
        'total_transactions',
        'sent_at',
        'status',
        'error_message',
    ];

    protected $casts = [
        'report_date' => 'date',
        'report_data' => 'array',
        'total_sales' => 'decimal:2',
        'total_items' => 'integer',
        'total_transactions' => 'integer',
        'sent_at' => 'datetime',
    ];
}

