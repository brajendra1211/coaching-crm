<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin\Plan;
use App\Models\SuperAdmin\Subscription;
use App\Models\SuperAdmin\Tenant;
use App\Models\SuperAdmin\TenantDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::with(['plan', 'activeDomain'])->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('slug', 'like', '%' . $request->search . '%')
                    ->orWhere('owner_email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tenants = $query->paginate(15)->withQueryString();

        return view('super-admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('super-admin.tenants.form', [
            'tenant' => new Tenant(['status' => 'active']),
            'plans' => Plan::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['database_name'] = $data['database_name'] ?: 'coaching_' . str_replace('-', '_', $data['slug']);
        $data['storage_path'] = $data['storage_path'] ?: 'tenants/' . $data['slug'];

        $tenant = Tenant::create($data);

        if (!empty($request->domain)) {
            $tenant->domains()->create([
                'domain' => strtolower($request->domain),
                'verification_token' => 'crm-verify=' . Str::random(40),
                'status' => 'pending',
            ]);
        }

        return redirect()->route('super-admin.tenants.show', $tenant)->with('success', 'Coaching onboarded successfully.');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['plan', 'domains', 'subscriptions', 'payments']);

        return view('super-admin.tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        return view('super-admin.tenants.form', [
            'tenant' => $tenant,
            'plans' => Plan::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Tenant $tenant)
    {
        $data = $this->validateData($request, $tenant->id);
        $data['slug'] = $data['slug'] ?: $tenant->slug;
        $data['database_name'] = $data['database_name'] ?: $tenant->database_name;
        $data['storage_path'] = $data['storage_path'] ?: $tenant->storage_path;

        $tenant->update($data);

        return redirect()->route('super-admin.tenants.show', $tenant)->with('success', 'Coaching updated successfully.');
    }

    public function updateStatus(Request $request, Tenant $tenant)
    {
        $data = $request->validate(['status' => ['required', 'in:active,inactive,suspended,expired']]);
        $tenant->update($data);

        return back()->with('success', 'Coaching status updated.');
    }

    public function provision(Tenant $tenant)
    {
        @set_time_limit(300);
        @ini_set('max_execution_time', '300');

        DB::connection('central')->statement('CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '', $tenant->database_name) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        File::ensureDirectoryExists(storage_path('app/public/' . trim($tenant->storage_path ?: ('tenants/' . $tenant->slug), '/')));

        config([
            'database.connections.tenant.database' => $tenant->database_name,
            'database.connections.tenant.host' => $tenant->database_host ?: env('TENANT_DB_HOST', env('DB_HOST', '127.0.0.1')),
            'database.connections.tenant.username' => $tenant->database_username ?: env('TENANT_DB_USERNAME', env('DB_USERNAME', 'root')),
            'database.connections.tenant.password' => $tenant->database_password ?: env('TENANT_DB_PASSWORD', env('DB_PASSWORD', '')),
        ]);

        DB::purge('tenant');
        DB::reconnect('tenant');
        Artisan::call('migrate', ['--database' => 'tenant', '--force' => true]);

        if ($tenant->owner_email) {
            DB::connection('tenant')->table('users')->updateOrInsert(
                ['email' => $tenant->owner_email],
                [
                    'name' => $tenant->owner_name ?: $tenant->name . ' Admin',
                    'email' => $tenant->owner_email,
                    'phone' => $tenant->owner_phone,
                    'password' => Hash::make('Admin@123'),
                    'role' => 'admin',
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        return back()->with('success', 'Tenant database and storage provisioned.');
    }

    public function extendSubscription(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'plan_id' => ['nullable', 'integer', 'exists:sa_plans,id'],
            'billing_cycle' => ['required', 'in:monthly,yearly,custom'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $subscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $data['plan_id'] ?: $tenant->plan_id,
            'billing_cycle' => $data['billing_cycle'],
            'amount' => $data['amount'] ?? 0,
            'starts_at' => $data['starts_at'] ?? now()->toDateString(),
            'ends_at' => $data['ends_at'],
            'status' => 'active',
            'notes' => $data['notes'] ?? null,
        ]);

        $tenant->update([
            'plan_id' => $subscription->plan_id,
            'subscription_ends_at' => $subscription->ends_at,
            'status' => 'active',
        ]);

        return back()->with('success', 'Subscription updated.');
    }

    public function addDomain(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'domain' => ['required', 'string', 'max:255', 'unique:sa_domains,domain'],
        ]);

        $tenant->domains()->create([
            'domain' => strtolower($data['domain']),
            'verification_token' => 'crm-verify=' . Str::random(40),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Domain added. Add DNS TXT record and verify.');
    }

    public function verifyDomain(TenantDomain $domain)
    {
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

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        $slugRule = 'unique:sa_tenants,slug';
        $dbRule = 'unique:sa_tenants,database_name';

        if ($ignoreId) {
            $slugRule .= ',' . $ignoreId;
            $dbRule .= ',' . $ignoreId;
        }

        return $request->validate([
            'plan_id' => ['nullable', 'integer', 'exists:sa_plans,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:120', $slugRule],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'owner_email' => ['nullable', 'email', 'max:255'],
            'owner_phone' => ['nullable', 'string', 'max:30'],
            'database_name' => ['nullable', 'string', 'max:120', $dbRule],
            'database_host' => ['nullable', 'string', 'max:255'],
            'database_username' => ['nullable', 'string', 'max:255'],
            'database_password' => ['nullable', 'string'],
            'storage_path' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive,suspended,expired'],
            'trial_ends_at' => ['nullable', 'date'],
            'subscription_ends_at' => ['nullable', 'date'],
            'domain' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
