@extends('super-admin.layouts.app')

@section('title', 'Plans')
@section('page_title', 'Plans')
@section('page_subtitle', 'Manage SaaS plans for coaching institutes')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Plans</h3>
        <a href="{{ route('super-admin.plans.create') }}" class="btn btn-primary">+ Add Plan</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Plan</th><th>Monthly</th><th>Yearly</th><th>Limits</th><th>Coachings</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
                @forelse($plans as $plan)
                    <tr>
                        <td><strong>{{ $plan->name }}</strong><br><small>{{ $plan->code }}</small></td>
                        <td>Rs. {{ number_format((float) $plan->monthly_price, 2) }}</td>
                        <td>Rs. {{ number_format((float) $plan->yearly_price, 2) }}</td>
                        <td>{{ $plan->student_limit ?: 'Unlimited' }} students<br>{{ $plan->storage_limit_mb ?: 'Unlimited' }} MB</td>
                        <td>{{ $plan->tenants_count }}</td>
                        <td><span class="pill {{ $plan->status === 'active' ? 'green' : 'orange' }}">{{ ucfirst($plan->status) }}</span></td>
                        <td><a href="{{ route('super-admin.plans.edit', $plan) }}" class="btn btn-primary">Edit</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;color:#64748b;">No plans found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:18px;">{{ $plans->links() }}</div>
</div>
@endsection
