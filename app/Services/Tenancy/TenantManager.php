<?php

namespace App\Services\Tenancy;

use App\Models\SuperAdmin\Tenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantManager
{
    private ?Tenant $tenant = null;

    public function setTenant(Tenant $tenant): void
    {
        $this->tenant = $tenant;

        Config::set('database.connections.tenant.host', $tenant->database_host ?: env('TENANT_DB_HOST', env('DB_HOST', '127.0.0.1')));
        Config::set('database.connections.tenant.database', $tenant->database_name);
        Config::set('database.connections.tenant.username', $tenant->database_username ?: env('TENANT_DB_USERNAME', env('DB_USERNAME', 'root')));
        Config::set('database.connections.tenant.password', $tenant->database_password ?: env('TENANT_DB_PASSWORD', env('DB_PASSWORD', '')));

        DB::purge('tenant');
        DB::setDefaultConnection('tenant');

        $storagePath = $tenant->storage_path ?: ('tenants/' . $tenant->slug);

        Config::set('filesystems.disks.public.root', storage_path('app/public/' . trim($storagePath, '/')));
        Config::set('filesystems.disks.public.url', url('/storage'));
    }

    public function clear(): void
    {
        $this->tenant = null;
        DB::setDefaultConnection(config('database.default', 'mysql'));
    }

    public function current(): ?Tenant
    {
        return $this->tenant;
    }
}
