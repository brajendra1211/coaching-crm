@extends('admin.layouts.app')

@section('title', 'Batch Fee Plans')
@section('page_title', 'Batch Fee Plans')

@section('content')

<style>
    .fee-toolbar {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 220px 150px auto;
        gap: 12px;
        margin-bottom: 18px;
    }

    .status-pill {
        display: inline-flex;
        padding: 7px 11px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        border: 1px solid;
        white-space: nowrap;
    }

    .status-active {
        background: #dcfce7;
        color: #166534;
        border-color: #bbf7d0;
    }

    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .action-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    @media(max-width: 950px) {
        .fee-toolbar {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Batch Fee Plans</h3>
            <p style="margin:6px 0 0;color:#64748b;">
                Manage batch-wise fee plans and auto fee assignment for students.
            </p>
        </div>

        <a href="{{ route('admin.batch-fee-plans.create') }}" class="btn btn-primary">
            + Add Fee Plan
        </a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" class="fee-toolbar">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search fee plan, batch, billing type..."
        >

        <select name="batch_id">
            <option value="">All Batches</option>
            @foreach($batches as $batch)
                <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                    {{ $batch->name }}
                </option>
            @endforeach
        </select>

        <select name="status">
            <option value="">All Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>

        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Plan</th>
                    <th>Batch</th>
                    <th>Billing</th>
                    <th>Total Fee</th>
                    <th>Due Day</th>
                    <th>Status</th>
                    <th width="220">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($plans as $plan)
                    <tr>
                        <td>
                            <strong>{{ $plan->title }}</strong>
                            <br>
                            <small style="color:#64748b;">
                                Effective:
                                {{ $plan->effective_from ? $plan->effective_from->format('d M Y') : '-' }}
                            </small>
                        </td>

                        <td>
                            {{ optional($plan->batch)->name ?: '-' }}
                            <br>
                            <small style="color:#64748b;">
                                {{ optional($plan->batch)->code ?: '-' }}
                            </small>
                        </td>

                        <td>{{ ucwords(str_replace('_', ' ', $plan->billing_type)) }}</td>

                        <td>
                            <strong>₹{{ number_format($plan->total_amount, 2) }}</strong>
                            <br>
                            <small style="color:#64748b;">
                                Tuition: ₹{{ number_format($plan->tuition_fee, 2) }}
                            </small>
                        </td>

                        <td>
                            {{ $plan->due_day ? 'Every month ' . $plan->due_day : '-' }}
                            <br>
                            <small style="color:#64748b;">
                                Fine: ₹{{ number_format($plan->fine_per_day, 2) }}/day
                            </small>
                        </td>

                        <td>
                            <span class="status-pill status-{{ $plan->status }}">
                                {{ ucfirst($plan->status) }}
                            </span>
                        </td>

                        <td>
                            <div class="action-row">
                                <a href="{{ route('admin.batch-fee-plans.edit', $plan) }}" class="btn btn-primary">
                                    Edit
                                </a>

                                <form method="POST" action="{{ route('admin.batch-fee-plans.destroy', $plan) }}" onsubmit="return confirm('Delete this fee plan?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:35px;color:#64748b;">
                            No fee plans found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:22px;">
        {{ $plans->links() }}
    </div>
</div>

@endsection