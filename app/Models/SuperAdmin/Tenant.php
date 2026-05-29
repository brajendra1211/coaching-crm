<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $connection = 'central';
    protected $table = 'sa_tenants';

    protected $fillable = [
        'plan_id',
        'name',
        'slug',
        'owner_name',
        'owner_email',
        'owner_phone',
        'database_name',
        'database_host',
        'database_username',
        'database_password',
        'storage_path',
        'status',
        'trial_ends_at',
        'subscription_ends_at',
        'last_notified_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'date',
        'subscription_ends_at' => 'date',
        'last_notified_at' => 'datetime',
        'database_password' => 'encrypted',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function domains()
    {
        return $this->hasMany(TenantDomain::class, 'tenant_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'tenant_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'tenant_id');
    }

    public function activeDomain()
    {
        return $this->hasOne(TenantDomain::class, 'tenant_id')->where('status', 'verified');
    }

    public function isUsable(): bool
    {
        return $this->status === 'active'
            && (!$this->subscription_ends_at || $this->subscription_ends_at->endOfDay()->isFuture());
    }

    public function loginUrl(string $portal = 'admin'): string
    {
        $domain = $this->activeDomain?->domain ?: $this->domains()->where('status', 'verified')->value('domain');
        $path = match ($portal) {
            'student' => '/student/login',
            'teacher' => '/teacher/login',
            'staff' => '/staff/login',
            default => '/admin/login',
        };

        if ($domain) {
            return 'https://' . $domain . $path;
        }

        $fallbackDomain = config('app.url') ?: url('/');

        return rtrim($fallbackDomain, '/') . $path . '?tenant=' . urlencode($this->slug);
    }
}
