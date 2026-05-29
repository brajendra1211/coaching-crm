@extends('portal.layouts.app', ['portalRole' => 'teacher'])
@section('title', 'Teacher Profile')
@section('page_title', 'Profile')
@section('page_subtitle', 'Teacher details and assigned batches')
@section('content')
@if(!$teacher)
    <div class="card"><h3>Profile Not Linked</h3><p class="muted" style="margin:0;">Ask admin to link this login with a teacher profile.</p></div>
@else
<div class="grid-2">
    <section class="card"><h3>Teacher Information</h3><div class="info-grid">
        <div class="info-item"><small>Name</small><strong>{{ $teacher->name }}</strong></div>
        <div class="info-item"><small>Phone</small><strong>{{ $teacher->phone ?: '-' }}</strong></div>
        <div class="info-item"><small>Email</small><strong>{{ $teacher->email ?: '-' }}</strong></div>
        <div class="info-item"><small>Status</small><strong>{{ ucfirst($teacher->status) }}</strong></div>
        <div class="info-item"><small>Qualification</small><strong>{{ $teacher->qualification ?: '-' }}</strong></div>
        <div class="info-item"><small>Experience</small><strong>{{ $teacher->experience ?: '-' }}</strong></div>
        <div class="info-item" style="grid-column:1/-1;"><small>Specialization</small><strong>{{ $teacher->specialization ?: '-' }}</strong></div>
    </div></section>
    <section class="card"><h3>Assigned Batches</h3><div class="table-wrap"><table>
        <thead><tr><th>Batch</th><th>Course</th><th>Subject</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($batches as $batch)
            <tr><td>{{ $batch->name }}</td><td>{{ $batch->course->title ?? '-' }}</td><td>{{ $batch->subject->name ?? '-' }}</td><td><span class="pill">{{ ucfirst($batch->pivot->status ?? $batch->status) }}</span></td></tr>
        @empty
            <tr><td colspan="4" style="text-align:center;color:#64748b;">No batches assigned.</td></tr>
        @endforelse
        </tbody>
    </table></div></section>
</div>
@endif
@endsection
