@extends('super-admin.layouts.app')

@section('title', 'Coachings')
@section('page_title', 'Coachings')
@section('page_subtitle', 'Onboard, activate, suspend and manage tenant databases')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>All Coachings</h3>
        <a href="{{ route('super-admin.tenants.create') }}" class="btn btn-primary">+ New Coaching</a>
    </div>
    <form method="GET" style="display:grid;grid-template-columns:minmax(0,1fr) 180px auto;gap:12px;margin-bottom:16px;">
        <input name="search" value="{{ request('search') }}" placeholder="Search name, slug, owner email">
        <select name="status"><option value="">All Status</option>@foreach(['active','inactive','suspended','expired'] as $status)<option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>@endforeach</select>
        <button class="btn btn-primary" type="submit">Search</button>
    </form>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Coaching</th><th>Owner</th><th>Plan</th><th>Domain</th><th>Database</th><th>Status</th><th>Expiry</th><th>Action</th></tr></thead>
            <tbody>
                @forelse($tenants as $tenant)
                    <tr>
                        <td><strong>{{ $tenant->name }}</strong><br><small>{{ $tenant->slug }}</small></td>
                        <td>{{ $tenant->owner_name ?: '-' }}<br><small>{{ $tenant->owner_email ?: '-' }}</small></td>
                        <td>{{ $tenant->plan->name ?? '-' }}</td>
                        <td>{{ $tenant->activeDomain->domain ?? '-' }}</td>
                        <td>{{ $tenant->database_name }}</td>
                        <td><span class="pill {{ $tenant->status === 'active' ? 'green' : 'orange' }}">{{ ucfirst($tenant->status) }}</span></td>
                        <td>{{ $tenant->subscription_ends_at ? $tenant->subscription_ends_at->format('d M Y') : '-' }}</td>
                        <td><a class="btn btn-primary" href="{{ route('super-admin.tenants.show', $tenant) }}">Manage</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center;color:#64748b;">No coachings found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:18px;">{{ $tenants->links() }}</div>
</div>
@endsection
