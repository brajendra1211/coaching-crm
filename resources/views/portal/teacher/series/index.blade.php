@extends('portal.layouts.app', ['portalRole' => 'teacher'])

@section('title', 'Exam Series')
@section('page_title', 'Exam Series')
@section('page_subtitle', 'Build free and paid reusable exam series')

@section('content')
@if(session('success'))
    <div class="card" style="margin-bottom:18px;background:#dcfce7;color:#166534;border-color:#bbf7d0;">{{ session('success') }}</div>
@endif
<div class="page-actions">
    <a href="{{ route('teacher.series.create') }}" class="btn btn-primary">Create Series</a>
    <a href="{{ route('teacher.exams.create') }}" class="btn btn-light">Create Exam</a>
</div>
<section class="card">
    <h3>Series</h3>
    <div class="table-wrap"><table>
        <thead><tr><th>Series</th><th>Batch</th><th>Access</th><th>Exams</th><th>Date</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
        @forelse($series as $item)
            <tr>
                <td><strong>{{ $item->title }}</strong><br><small class="muted">{{ $item->label ?: ucfirst($item->difficulty) }}</small></td>
                <td>{{ $item->batch->name ?? 'All Assigned' }}</td>
                <td>{{ ucfirst($item->access_type) }}{{ $item->access_type === 'paid' ? ' - Rs. ' . number_format((float) $item->price, 2) : '' }}</td>
                <td>{{ $item->exams_count }}</td>
                <td>{{ $item->start_date ? $item->start_date->format('d M Y') : '-' }}</td>
                <td><span class="pill">{{ ucfirst($item->status) }}</span></td>
                <td style="display:flex;gap:8px;flex-wrap:wrap;"><a href="{{ route('teacher.series.builder', $item) }}" class="btn btn-primary">Builder</a><a href="{{ route('teacher.series.edit', $item) }}" class="btn btn-light">Edit</a></td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;color:#64748b;">No series found.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    {{ $series->links() }}
</section>
@endsection
