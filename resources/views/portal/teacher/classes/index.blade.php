@extends('portal.layouts.app', ['portalRole' => 'teacher'])
@section('title', 'Online Classes')
@section('page_title', 'Online Classes')
@section('page_subtitle', 'Class schedule and meeting links')
@section('content')
@if(!$teacher)
    <div class="card" style="margin-bottom:18px;">
        <h3>Profile Not Linked</h3>
        <p class="muted" style="margin:0;">Ask admin to link this user with a teacher profile.</p>
    </div>
@endif
<div class="page-actions"><a href="{{ url('/teacher/classes') }}" class="btn btn-primary">Class Schedule</a></div>
<section class="card">
    <h3>Classes</h3>
    <div class="table-wrap"><table>
        <thead><tr><th>Class</th><th>Batch</th><th>Subject</th><th>Date</th><th>Time</th><th>Platform</th><th>Status</th><th>Open</th></tr></thead>
        <tbody>
        @forelse($classes as $class)
            <tr>
                <td><strong>{{ $class->title }}</strong></td>
                <td>{{ $class->batch->name ?? 'All' }}</td>
                <td>{{ $class->subject->name ?? '-' }}</td>
                <td>{{ $class->class_date ? $class->class_date->format('d M Y') : '-' }}</td>
                <td>{{ $class->start_time ?: '-' }} - {{ $class->end_time ?: '-' }}</td>
                <td>{{ $class->platform ?: '-' }}</td>
                <td><span class="pill">{{ ucfirst($class->status) }}</span></td>
                <td>@if($class->meeting_link)<a href="{{ $class->meeting_link }}" target="_blank" class="btn btn-light">Open</a>@else - @endif</td>
            </tr>
        @empty
            <tr><td colspan="8" style="text-align:center;color:#64748b;">No classes found.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    {{ $classes->links() }}
</section>
@endsection
