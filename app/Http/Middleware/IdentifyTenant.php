<?php

namespace App\Http\Middleware;

use App\Models\SuperAdmin\TenantDomain;
use App\Models\SuperAdmin\Tenant;
use App\Services\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('super-admin*')) {
            return $next($request);
        }

        $tenant = $this->tenantFromRequest($request);

        if ($tenant && $tenant->isUsable()) {
            app(TenantManager::class)->setTenant($tenant);
            $request->session()->put('tenant_id', $tenant->id);

            return $next($request);
        }

        if ($this->isCentralDomain($request)) {
            return $next($request);
        }

        $host = strtolower($request->getHost());
        $domain = TenantDomain::with('tenant.plan')
            ->where('domain', $host)
            ->where('status', 'verified')
            ->first();

        if (!$domain || !$domain->tenant || !$domain->tenant->isUsable()) {
            return $next($request);
        }

        app(TenantManager::class)->setTenant($domain->tenant);
        $request->session()->put('tenant_id', $domain->tenant->id);

        return $next($request);
    }

    private function tenantFromRequest(Request $request): ?Tenant
    {
        if ($request->filled('tenant')) {
            return Tenant::where('slug', $request->query('tenant'))->first();
        }

        if ($request->session()->has('tenant_id')) {
            return Tenant::find($request->session()->get('tenant_id'));
        }

        return null;
    }

    private function isCentralDomain(Request $request): bool
    {
        $host = strtolower($request->getHost());

        return in_array($host, array_map('strtolower', config('tenancy.central_domains', [])), true);
    }
}
