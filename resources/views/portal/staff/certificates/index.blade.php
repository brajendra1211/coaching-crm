@extends('portal.layouts.app', ['portalRole' => 'staff'])
@section('title', 'Certificates')
@section('page_title', 'Certificates')
@section('page_subtitle', 'Issued certificates and status')
@section('content')
<div class="page-actions"><a href="{{ route('admin.certificates.create') }}" class="btn btn-primary">Issue Certificate</a></div>
<section class="card"><h3>Certificates</h3><div class="table-wrap"><table>
<thead><tr><th>No</th><th>Student</th><th>Title</th><th>Type</th><th>Issue Date</th><th>Status</th></tr></thead><tbody>
@forelse($certificates as $certificate)
<tr><td>{{ $certificate->certificate_no }}</td><td>{{ $certificate->student->name ?? $certificate->recipient_name }}</td><td>{{ $certificate->certificate_title }}</td><td>{{ ucfirst($certificate->certificate_type) }}</td><td>{{ $certificate->issue_date ? $certificate->issue_date->format('d M Y') : '-' }}</td><td><span class="pill {{ $certificate->status === 'active' ? 'green' : 'red' }}">{{ ucfirst($certificate->status) }}</span></td></tr>
@empty <tr><td colspan="6" style="text-align:center;color:#64748b;">No certificates found.</td></tr> @endforelse
</tbody></table></div>{{ $certificates->links() }}</section>
@endsection
