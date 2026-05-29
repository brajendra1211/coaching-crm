<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin\TenantDomain;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantDomainController extends Controller
{
    public function index()
    {
        $tenant = app(TenantManager::class)->current();

        if (!$tenant) {
            return view('admin.domains.index', [
                'tenant' => null,
                'domains' => collect(),
            ]);
        }

        return view('admin.domains.index', [
            'tenant' => $tenant,
            'domains' => $tenant->domains()->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $tenant = app(TenantManager::class)->current();

        abort_unless($tenant, 404);

        $data = $request->validate([
            'domain' => ['required', 'string', 'max:255', 'unique:central.sa_domains,domain'],
        ]);

        $tenant->domains()->create([
            'domain' => strtolower($data['domain']),
            'verification_token' => 'crm-verify=' . Str::random(40),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Domain added. Add DNS TXT record and verify.');
    }

    public function verify(TenantDomain $domain)
    {
        $tenant = app(TenantManager::class)->current();

        abort_unless($tenant && (int) $domain->tenant_id === (int) $tenant->id, 403);

        $records = @dns_get_record($domain->domain, DNS_TXT) ?: [];
        $verified = collect($records)->contains(function ($record) use ($domain) {
            return str_contains(implode(' ', $record['entries'] ?? [$record['txt'] ?? '']), $domain->verification_token);
        });

        if ($verified) {
            $domain->update(['status' => 'verified', 'verified_at' => now()]);

            return back()->with('success', 'Domain verified successfully.');
        }

        return back()->with('error', 'DNS TXT verification record not found yet.');
    }
}
