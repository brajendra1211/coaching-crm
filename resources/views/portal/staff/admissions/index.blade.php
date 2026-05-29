@extends('portal.layouts.app', ['portalRole' => 'staff'])
@section('title', 'Admissions')
@section('page_title', 'Admissions')
@section('page_subtitle', 'Admission applications and student onboarding')
@section('content')
<div class="page-actions"><a href="{{ route('admin.admissions.create') }}" class="btn btn-primary">New Admission</a></div>
<section class="card"><h3>Admissions</h3><div class="table-wrap"><table>
<thead><tr><th>No</th><th>Student</th><th>Course</th><th>Phone</th><th>Date</th><th>Status</th></tr></thead><tbody>
@forelse($admissions as $admission)
<tr><td>{{ $admission->admission_no }}</td><td>{{ $admission->student_name }}</td><td>{{ $admission->course->title ?? $admission->course_name ?? '-' }}</td><td>{{ $admission->student_phone ?: '-' }}</td><td>{{ $admission->admission_date ? $admission->admission_date->format('d M Y') : '-' }}</td><td><span class="pill">{{ ucfirst($admission->status) }}</span></td></tr>
@empty <tr><td colspan="6" style="text-align:center;color:#64748b;">No admissions found.</td></tr> @endforelse
</tbody></table></div>{{ $admissions->links() }}</section>
@endsection
