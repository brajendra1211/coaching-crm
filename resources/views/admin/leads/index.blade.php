@extends('admin.layouts.app')

@section('title', 'Leads')
@section('page_title', 'Lead Management')

@section('content')

<div class="card">
    <div class="card-header">
        <div>
            <h3>All Enquiries</h3>
            <p style="margin:6px 0 0;color:#64748b;">Manage website enquiries and follow-ups.</p>
        </div>
    </div>

    <form method="GET" style="display:grid;grid-template-columns:1fr 220px 220px auto;gap:12px;margin-bottom:18px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, phone or email">

        <select name="status">
            <option value="">All Status</option>
            <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
            <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
            <option value="interested" {{ request('status') === 'interested' ? 'selected' : '' }}>Interested</option>
            <option value="not_interested" {{ request('status') === 'not_interested' ? 'selected' : '' }}>Not Interested</option>
            <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>Converted</option>
            <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Lost</option>
        </select>

        <select name="source">
            <option value="">All Sources</option>
            <option value="website_contact" {{ request('source') === 'website_contact' ? 'selected' : '' }}>Contact Page</option>
            <option value="website" {{ request('source') === 'website' ? 'selected' : '' }}>Website</option>
            <option value="website_admission" {{ request('source') === 'website_admission' ? 'selected' : '' }}>Admission Page</option>
        </select>

        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Contact</th>
                    <th>Course</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th>Follow-up</th>
                    <th width="170">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($leads as $lead)
                    <tr>
                        <td>
                            <strong>{{ $lead->name }}</strong><br>
                            <small>{{ $lead->class_level ?: 'N/A' }}</small>
                        </td>

                        <td>
                            <strong>{{ $lead->phone }}</strong><br>
                            <small>{{ $lead->email ?: 'No email' }}</small>
                        </td>

                        <td>{{ $lead->course->title ?? 'General Enquiry' }}</td>

                        <td>{{ $lead->source ?: 'Website' }}</td>

                        <td>
                            <span class="badge {{ $lead->status === 'converted' ? 'badge-active' : ($lead->status === 'lost' ? 'badge-inactive' : 'badge-pending') }}">
                                {{ ucwords(str_replace('_', ' ', $lead->status)) }}
                            </span>
                        </td>

                        <td>{{ $lead->follow_up_date ? $lead->follow_up_date->format('d M Y') : 'Not Set' }}</td>

                        <td>
                            <a href="{{ route('admin.leads.show', $lead) }}" class="btn btn-primary">View</a>

                            <form method="POST" action="{{ route('admin.leads.destroy', $lead) }}" class="action-form" onsubmit="return confirm('Delete this lead?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;">No leads found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:18px;">
        {{ $leads->links() }}
    </div>
</div>

@endsection
