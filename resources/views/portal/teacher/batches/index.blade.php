@extends('portal.layouts.app', ['portalRole' => 'teacher'])
@section('title', 'My Batches')
@section('page_title', 'My Batches')
@section('page_subtitle', 'Assigned batches, subjects and student strength')
@section('content')
<section class="card">
    <h3>Assigned Batches</h3>
    <div class="table-wrap"><table>
        <thead><tr><th>Batch</th><th>Course</th><th>Subject</th><th>Class</th><th>Time</th><th>Students</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($batches as $batch)
            <tr>
                <td><strong>{{ $batch->name }}</strong><br><small class="muted">{{ $batch->code ?: '-' }}</small></td>
                <td>{{ $batch->course->title ?? '-' }}</td>
                <td>{{ $batch->subject->name ?? '-' }}</td>
                <td>{{ $batch->class_level ?: '-' }}</td>
                <td>{{ $batch->start_time ?: '-' }} - {{ $batch->end_time ?: '-' }}</td>
                <td>{{ $batch->students_count }}</td>
                <td><span class="pill">{{ ucfirst($batch->status) }}</span></td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;color:#64748b;">No batches assigned.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    {{ $batches->links() }}
</section>
@endsection
