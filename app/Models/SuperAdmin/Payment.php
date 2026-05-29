<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $connection = 'central';
    protected $table = 'sa_payments';

    protected $fillable = [
        'tenant_id',
        'subscription_id',
        'invoice_no',
        'amount',
        'payment_date',
        'payment_mode',
        'transaction_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];
}
