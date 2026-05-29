<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Model;

class TenantDomain extends Model
{
    protected $connection = 'central';
    protected $table = 'sa_domains';

    protected $fillable = [
        'tenant_id',
        'domain',
        'verification_token',
        'verification_method',
        'status',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
