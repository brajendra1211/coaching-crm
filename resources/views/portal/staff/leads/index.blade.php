@extends('portal.layouts.app', ['portalRole' => 'staff'])
@section('title', 'Leads')
@section('page_title', 'Leads')
@section('page_subtitle', 'Website enquiries and follow-up pipeline')
@section('content')
<div class="page-actions"><a href="{{ route('admin.leads.index') }}" class="btn btn-primary">Manage Leads</a></div>
<section class="card"><h3>Leads</h3><div class="table-wrap"><table>
<thead><tr><th>Name</th><th>Phone</th><th>Email</th><th>Course</th><th>Source</th><th>Follow Up</th><th>Status</th></tr></thead><tbody>
@forelse($leads as $lead)
<tr><td>{{ $lead->name }}</td><td>{{ $lead->phone }}</td><td>{{ $lead->email ?: '-' }}</td><td>{{ $lead->course->title ?? '-' }}</td><td>{{ $lead->source ?: '-' }}</td><td>{{ $lead->follow_up_date ?: '-' }}</td><td><span class="pill">{{ ucfirst($lead->status) }}</span></td></tr>
@empty <tr><td colspan="7" style="text-align:center;color:#64748b;">No leads found.</td></tr> @endforelse
</tbody></table></div>{{ $leads->links() }}</section>
@endsection
