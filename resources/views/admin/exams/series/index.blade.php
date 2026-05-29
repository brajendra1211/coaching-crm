@extends('admin.layouts.app')

@section('title', 'Exam Series')
@section('page_title', 'Exam Series')

@section('content')
<style>
    .toolbar { display:grid; grid-template-columns:minmax(0,1fr) 180px auto; gap:12px; margin-bottom:18px; }
    .pill { display:inline-flex; padding:7px 11px; border-radius:999px; font-size:12px; font-weight:900; border:1px solid #bfdbfe; background:#eff6ff; color:#1d4ed8; }
    .actions { display:flex; gap:8px; flex-wrap:wrap; }
    @media(max-width:800px){ .toolbar{grid-template-columns:1fr;} }
</style>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Exam Series</h3>
            <p style="margin:6px 0 0;color:#64748b;">Single exams ko reuse karke free ya paid test series banayein.</p>
        </div>
        <a href="{{ route('admin.exam-series.create') }}" class="btn btn-primary">+ Add Series</a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">{{ session('success') }}</div>
    @endif

    <form method="GET" class="toolbar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search series title, code or label...">
        <select name="access_type">
            <option value="">Free + Paid</option>
            <option value="free" {{ request('access_type') === 'free' ? 'selected' : '' }}>Free</option>
            <option value="paid" {{ request('access_type') === 'paid' ? 'selected' : '' }}>Paid</option>
        </select>
        <button class="btn btn-primary" type="submit">Search</button>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Series</th><th>Batch</th><th>Label</th><th>Access</th><th>Dates</th><th>Exams</th><th>Status</th><th width="270">Action</th></tr></thead>
            <tbody>
                @forelse($series as $item)
                    <tr>
                        <td><strong>{{ $item->title }}</strong><br><small style="color:#64748b;">{{ $item->series_code ?: '-' }}</small></td>
                        <td>{{ $item->batch->name ?? '-' }}</td>
                        <td>{{ $item->label ?: ucfirst($item->difficulty) }}</td>
                        <td><span class="pill">{{ ucfirst($item->access_type) }}</span><br><small style="color:#64748b;">{{ $item->access_type === 'paid' ? 'Rs. ' . number_format((float) $item->price, 2) : 'No fee' }}</small></td>
                        <td>{{ $item->start_date ? $item->start_date->format('d M Y') : '-' }} - {{ $item->end_date ? $item->end_date->format('d M Y') : '-' }}</td>
                        <td>{{ $item->exams_count }}</td>
                        <td>{{ ucfirst($item->status) }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.exam-series.builder', $item) }}" class="btn btn-dark">Builder</a>
                                <a href="{{ route('admin.exam-series.edit', $item) }}" class="btn btn-primary">Edit</a>
                                <form method="POST" action="{{ route('admin.exam-series.destroy', $item) }}" onsubmit="return confirm('Delete this series?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center;padding:35px;color:#64748b;">No exam series found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:22px;">{{ $series->links() }}</div>
</div>
@endsection
