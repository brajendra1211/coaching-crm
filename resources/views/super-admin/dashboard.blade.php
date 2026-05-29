@extends('super-admin.layouts.app')

@section('title', 'Super Admin Dashboard')
@section('page_title', 'Super Admin Dashboard')
@section('page_subtitle', 'All coachings, plans, subscriptions and expiry alerts')

@section('content')
<div class="grid-4">
    <div class="card stat"><small>Total Coachings</small><strong>{{ $totalTenants }}</strong></div>
    <div class="card stat"><small>Active Coachings</small><strong>{{ $activeTenants }}</strong></div>
    <div class="card stat"><small>Expired / Due</small><strong>{{ $expiredTenants }}</strong></div>
    <div class="card stat"><small>This Month Revenue</small><strong>Rs. {{ number_format((float) $monthlyRevenue, 2) }}</strong></div>
</div>

<div class="grid-2">
    <section class="card">
        <div class="card-header">
            <h3>Recent Coachings</h3>
            <a href="{{ route('super-admin.tenants.create') }}" class="btn btn-primary">+ New Coaching</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Coaching</th><th>Plan</th><th>Domain</th><th>Status</th><th>Expiry</th></tr></thead>
                <tbody>
                    @forelse($recentTenants as $tenant)
                        <tr>
                            <td><a href="{{ route('super-admin.tenants.show', $tenant) }}"><strong>{{ $tenant->name }}</strong></a><br><small>{{ $tenant->owner_email }}</small></td>
                            <td>{{ $tenant->plan->name ?? '-' }}</td>
                            <td>{{ $tenant->activeDomain->domain ?? '-' }}</td>
                            <td><span class="pill {{ $tenant->status === 'active' ? 'green' : 'orange' }}">{{ ucfirst($tenant->status) }}</span></td>
                            <td>{{ $tenant->subscription_ends_at ? $tenant->subscription_ends_at->format('d M Y') : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;color:#64748b;">No coachings onboarded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="card">
        <div class="card-header"><h3>Expiring Soon</h3></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Coaching</th><th>Plan</th><th>Expires</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($expiringSoon as $tenant)
                        <tr>
                            <td>{{ $tenant->name }}</td>
                            <td>{{ $tenant->plan->name ?? '-' }}</td>
                            <td>{{ $tenant->subscription_ends_at ? $tenant->subscription_ends_at->format('d M Y') : '-' }}</td>
                            <td><a class="btn btn-light" href="{{ route('super-admin.tenants.show', $tenant) }}">Manage</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;color:#64748b;">No expiry alerts.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
