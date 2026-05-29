<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $connection = 'central';
    protected $table = 'sa_plans';

    protected $fillable = [
        'name',
        'code',
        'monthly_price',
        'yearly_price',
        'student_limit',
        'staff_limit',
        'storage_limit_mb',
        'features',
        'status',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
    ];

    public function tenants()
    {
        return $this->hasMany(Tenant::class, 'plan_id');
    }
}
