<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $connection = 'central';
    protected $table = 'sa_subscriptions';

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'billing_cycle',
        'amount',
        'starts_at',
        'ends_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'starts_at' => 'date',
        'ends_at' => 'date',
    ];
}
