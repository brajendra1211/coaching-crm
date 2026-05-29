@extends('portal.layouts.app', ['portalRole' => 'staff'])

@section('title', 'Staff Dashboard')
@section('page_title', 'Staff Dashboard')
@section('page_subtitle', 'Admissions, students, fees and daily operations')

@section('content')
<div class="page-actions">
    <a href="{{ route('admin.admissions.create') }}" class="btn btn-primary">New Admission</a>
    <a href="{{ route('admin.fee-collections.create') }}" class="btn btn-light">Collect Fee</a>
    <a href="{{ route('admin.students.create') }}" class="btn btn-light">Add Student</a>
</div>

<div class="grid-4">
    <div class="card stat"><small>Leads</small><strong>{{ $leadsCount }}</strong></div>
    <div class="card stat"><small>Admissions</small><strong>{{ $admissionsCount }}</strong></div>
    <div class="card stat"><small>Students</small><strong>{{ $studentsCount }}</strong></div>
    <div class="card stat"><small>Today Collection</small><strong>Rs. {{ number_format((float) $todayCollection, 2) }}</strong></div>
</div>

<div class="grid-2">
    <section class="card">
        <h3>Recent Leads</h3>
        <div class="table-wrap"><table>
            <thead><tr><th>Name</th><th>Phone</th><th>Course</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($leads as $lead)
                <tr><td>{{ $lead->name }}</td><td>{{ $lead->phone }}</td><td>{{ $lead->course->title ?? '-' }}</td><td><span class="pill">{{ ucfirst($lead->status) }}</span></td></tr>
            @empty
                <tr><td colspan="4" style="text-align:center;color:#64748b;">No leads found.</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </section>

    <section class="card">
        <h3>Recent Admissions</h3>
        <div class="table-wrap"><table>
            <thead><tr><th>Student</th><th>Course</th><th>Date</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($admissions as $admission)
                <tr><td>{{ $admission->student_name }}</td><td>{{ $admission->course->title ?? $admission->course_name ?? '-' }}</td><td>{{ $admission->admission_date ? $admission->admission_date->format('d M Y') : '-' }}</td><td><span class="pill">{{ ucfirst($admission->status) }}</span></td></tr>
            @empty
                <tr><td colspan="4" style="text-align:center;color:#64748b;">No admissions found.</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </section>
</div>

<div class="grid-2">
    <section class="card">
        <h3>Recent Students</h3>
        <div class="table-wrap"><table>
            <thead><tr><th>Student</th><th>Class</th><th>Phone</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($students as $student)
                <tr><td><strong>{{ $student->name }}</strong><br><small>{{ $student->student_code }}</small></td><td>{{ $student->class_level ?: '-' }}</td><td>{{ $student->phone ?: '-' }}</td><td><span class="pill">{{ ucfirst($student->status) }}</span></td></tr>
            @empty
                <tr><td colspan="4" style="text-align:center;color:#64748b;">No students found.</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </section>

    <section class="card">
        <h3>Fee Payments</h3>
        <div class="table-wrap"><table>
            <thead><tr><th>Receipt</th><th>Student</th><th>Amount</th><th>Date</th></tr></thead>
            <tbody>
            @forelse($payments as $payment)
                <tr><td>{{ $payment->receipt_no }}</td><td>{{ $payment->student->name ?? '-' }}</td><td>Rs. {{ number_format((float) $payment->amount, 2) }}</td><td>{{ $payment->payment_date ? $payment->payment_date->format('d M Y') : '-' }}</td></tr>
            @empty
                <tr><td colspan="4" style="text-align:center;color:#64748b;">No payments found.</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </section>
</div>
@endsection
