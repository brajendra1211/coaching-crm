@extends('portal.layouts.app', ['portalRole' => 'staff'])
@section('title', 'Materials')
@section('page_title', 'Materials')
@section('page_subtitle', 'Study resources shared in the coaching')
@section('content')
<div class="page-actions"><a href="{{ route('admin.study-materials.index') }}" class="btn btn-primary">Manage Materials</a></div>
<section class="card"><h3>Materials</h3><div class="table-wrap"><table>
<thead><tr><th>Title</th><th>Type</th><th>Batch</th><th>Subject</th><th>Teacher</th><th>Status</th></tr></thead><tbody>
@forelse($materials as $material)
<tr><td>{{ $material->title }}</td><td>{{ ucfirst($material->type ?? '-') }}</td><td>{{ $material->batch->name ?? 'All' }}</td><td>{{ $material->subject->name ?? '-' }}</td><td>{{ $material->teacher->name ?? '-' }}</td><td><span class="pill">{{ ucfirst($material->status) }}</span></td></tr>
@empty <tr><td colspan="6" style="text-align:center;color:#64748b;">No materials found.</td></tr> @endforelse
</tbody></table></div>{{ $materials->links() }}</section>
@endsection
