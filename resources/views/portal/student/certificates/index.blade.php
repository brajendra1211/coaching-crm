@extends('portal.layouts.app', ['portalRole' => 'student'])

@section('title', 'Certificates')
@section('page_title', 'Certificates')
@section('page_subtitle', 'Issued certificates and downloadable PDF copies')

@section('content')
<section class="card">
    <h3>My Certificates</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Certificate No</th><th>Title</th><th>Type</th><th>Course</th><th>Grade</th><th>Issue Date</th><th>Status</th><th>Download</th></tr></thead>
            <tbody>
                @forelse($certificates as $certificate)
                    <tr>
                        <td><strong>{{ $certificate->certificate_no }}</strong></td>
                        <td>{{ $certificate->certificate_title }}</td>
                        <td>{{ ucfirst($certificate->certificate_type) }}</td>
                        <td>{{ $certificate->course_name ?: '-' }}</td>
                        <td>{{ $certificate->grade ?: '-' }}</td>
                        <td>{{ $certificate->issue_date ? $certificate->issue_date->format('d M Y') : '-' }}</td>
                        <td><span class="pill green">{{ ucfirst($certificate->status) }}</span></td>
                        <td><a href="{{ route('student.certificates.pdf', $certificate) }}" class="btn btn-light">PDF</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center;color:#64748b;">No certificate issued yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $certificates->links() }}
</section>
@endsection
