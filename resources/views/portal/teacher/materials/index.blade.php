@extends('portal.layouts.app', ['portalRole' => 'teacher'])
@section('title', 'Study Materials')
@section('page_title', 'Study Materials')
@section('page_subtitle', 'Materials shared with your batches')
@section('content')
@if(!$teacher)
    <div class="card" style="margin-bottom:18px;">
        <h3>Profile Not Linked</h3>
        <p class="muted" style="margin:0;">Ask admin to link this user with a teacher profile.</p>
    </div>
@endif
@if(session('success'))
    <div class="card" style="margin-bottom:18px;background:#dcfce7;color:#166534;border-color:#bbf7d0;">{{ session('success') }}</div>
@endif
<div class="page-actions">
    <a href="{{ route('teacher.materials.create') }}" class="btn btn-primary">Add Material</a>
    <a href="{{ url('/teacher/materials') }}" class="btn btn-light">Refresh</a>
</div>
<section class="card">
    <h3>Materials</h3>
    <div class="table-wrap"><table>
        <thead><tr><th>Title</th><th>Type</th><th>Class</th><th>Batch</th><th>Subject</th><th>Status</th><th>Open</th></tr></thead>
        <tbody>
        @forelse($materials as $material)
            <tr>
                <td><strong>{{ $material->title }}</strong></td>
                <td>{{ ucfirst($material->type ?? '-') }}</td>
                <td>{{ $material->class_level ?: ($material->batch->class_level ?? '-') }}</td>
                <td>{{ $material->batch->name ?? 'All' }}</td>
                <td>{{ $material->subject->name ?? '-' }}</td>
                <td><span class="pill">{{ ucfirst($material->status) }}</span></td>
                <td>
                    @if($material->file_path)<a class="btn btn-light" href="{{ asset('storage/' . $material->file_path) }}" target="_blank">File</a>
                    @elseif($material->external_link)<a class="btn btn-light" href="{{ $material->external_link }}" target="_blank">Link</a>
                    @else - @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;color:#64748b;">No materials found.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    {{ $materials->links() }}
</section>
@endsection
