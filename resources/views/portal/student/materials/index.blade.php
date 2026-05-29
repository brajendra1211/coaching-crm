@extends('portal.layouts.app', ['portalRole' => 'student'])

@section('title', 'Study Materials')
@section('page_title', 'Study Materials')
@section('page_subtitle', 'Notes, PDFs, links and learning resources shared by teachers')

@section('content')
<section class="card">
    <h3>Available Materials</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Title</th><th>Type</th><th>Batch</th><th>Subject</th><th>Teacher</th><th>Description</th><th>Open</th></tr></thead>
            <tbody>
                @forelse($materials as $material)
                    <tr>
                        <td><strong>{{ $material->title }}</strong></td>
                        <td>{{ ucfirst($material->type ?? '-') }}</td>
                        <td>{{ $material->batch->name ?? 'All' }}</td>
                        <td>{{ $material->subject->name ?? '-' }}</td>
                        <td>{{ $material->teacher->name ?? '-' }}</td>
                        <td>{{ \Illuminate\Support\Str::limit(strip_tags($material->description ?? ''), 80) ?: '-' }}</td>
                        <td>
                            @if($material->file_path)
                                <a class="btn btn-light" href="{{ asset('storage/' . $material->file_path) }}" target="_blank">Open File</a>
                            @elseif($material->external_link)
                                <a class="btn btn-light" href="{{ $material->external_link }}" target="_blank">Open Link</a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;color:#64748b;">No materials available.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $materials->links() }}
</section>
@endsection
