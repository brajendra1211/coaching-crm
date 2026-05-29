@extends('portal.layouts.app', ['portalRole' => 'student'])

@section('title', 'Attendance')
@section('page_title', 'Attendance')
@section('page_subtitle', 'Daily class attendance and overall percentage')

@section('content')
@php $percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0; @endphp
<div class="grid-4">
    <div class="card stat"><small>Total Marked</small><strong>{{ $total }}</strong></div>
    <div class="card stat"><small>Present</small><strong>{{ $present }}</strong></div>
    <div class="card stat"><small>Absent</small><strong>{{ $absent }}</strong></div>
    <div class="card stat"><small>Attendance</small><strong>{{ $percentage }}%</strong></div>
</div>

<section class="card">
    <h3>Attendance Log</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Date</th><th>Batch</th><th>Status</th><th>Note</th></tr></thead>
            <tbody>
                @forelse($attendance as $row)
                    <tr>
                        <td>{{ $row->attendance_date ? $row->attendance_date->format('d M Y') : '-' }}</td>
                        <td>{{ $row->batch->name ?? '-' }}</td>
                        <td><span class="pill {{ $row->status === 'absent' ? 'red' : 'green' }}">{{ ucfirst($row->status) }}</span></td>
                        <td>{{ $row->note ?: '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;color:#64748b;">No attendance found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $attendance->links() }}
</section>
@endsection
