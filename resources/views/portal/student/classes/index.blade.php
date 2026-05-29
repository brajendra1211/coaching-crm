@extends('portal.layouts.app', ['portalRole' => 'student'])

@section('title', 'Online Classes')
@section('page_title', 'Online Classes')
@section('page_subtitle', 'Live classes, scheduled sessions and meeting links')

@section('content')
<section class="card">
    <h3>Class Schedule</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Class</th><th>Batch</th><th>Subject</th><th>Teacher</th><th>Date</th><th>Time</th><th>Platform</th><th>Status</th><th>Join</th></tr></thead>
            <tbody>
                @forelse($classes as $class)
                    <tr>
                        <td><strong>{{ $class->title }}</strong><br><small class="muted">{{ \Illuminate\Support\Str::limit(strip_tags($class->description ?? ''), 70) }}</small></td>
                        <td>{{ $class->batch->name ?? 'All' }}</td>
                        <td>{{ $class->subject->name ?? '-' }}</td>
                        <td>{{ $class->teacher->name ?? '-' }}</td>
                        <td>{{ $class->class_date ? $class->class_date->format('d M Y') : '-' }}</td>
                        <td>{{ $class->start_time ?: '-' }} - {{ $class->end_time ?: '-' }}</td>
                        <td>{{ $class->platform ?: '-' }}</td>
                        <td><span class="pill {{ $class->status === 'live' ? 'green' : '' }}">{{ ucfirst($class->status) }}</span></td>
                        <td>
                            @if($class->meeting_link)
                                <a class="btn btn-primary" href="{{ $class->meeting_link }}" target="_blank">Join</a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" style="text-align:center;color:#64748b;">No online classes found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $classes->links() }}
</section>
@endsection
