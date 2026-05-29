@extends('portal.layouts.app', ['portalRole' => 'staff'])
@section('title', 'Exams')
@section('page_title', 'Exams')
@section('page_subtitle', 'Exam schedule and result operations')
@section('content')
<div class="page-actions"><a href="{{ route('admin.exams.index') }}" class="btn btn-primary">Manage Exams</a><a href="{{ route('admin.results.index') }}" class="btn btn-light">Results</a></div>
<section class="card"><h3>Exams</h3><div class="table-wrap"><table>
<thead><tr><th>Exam</th><th>Batch</th><th>Subject</th><th>Date</th><th>Marks</th><th>Status</th></tr></thead><tbody>
@forelse($exams as $exam)
<tr><td><strong>{{ $exam->title }}</strong></td><td>{{ $exam->batch->name ?? 'All' }}</td><td>{{ $exam->subject->name ?? '-' }}</td><td>{{ $exam->exam_date ? $exam->exam_date->format('d M Y') : '-' }}</td><td>{{ $exam->total_marks }}</td><td><span class="pill">{{ ucfirst($exam->status) }}</span></td></tr>
@empty <tr><td colspan="6" style="text-align:center;color:#64748b;">No exams found.</td></tr> @endforelse
</tbody></table></div>{{ $exams->links() }}</section>
@endsection
