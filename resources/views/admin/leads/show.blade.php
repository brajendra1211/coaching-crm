@extends('admin.layouts.app')

@section('title', 'Lead Details')
@section('page_title', 'Lead Details')

@section('content')

<div style="display:grid;grid-template-columns:1fr 420px;gap:22px;align-items:start;">
    <div class="card">
        <h3 style="margin-top:0;">Student Information</h3>

        <table class="table" style="min-width:0;">
            <tr>
                <th>Name</th>
                <td>{{ $lead->name }}</td>
            </tr>

            <tr>
                <th>Phone</th>
                <td>{{ $lead->phone }}</td>
            </tr>

            <tr>
                <th>Email</th>
                <td>{{ $lead->email ?: 'N/A' }}</td>
            </tr>

            <tr>
                <th>Class / Level</th>
                <td>{{ $lead->class_level ?: 'N/A' }}</td>
            </tr>

            <tr>
                <th>Interested Course</th>
                <td>{{ $lead->course->title ?? 'General Enquiry' }}</td>
            </tr>

            <tr>
                <th>Source</th>
                <td>{{ $lead->source ?: 'Website' }}</td>
            </tr>

            <tr>
                <th>Message</th>
                <td>{{ $lead->message ?: 'No message' }}</td>
            </tr>

            <tr>
                <th>Submitted On</th>
                <td>{{ $lead->created_at->format('d M Y, h:i A') }}</td>
            </tr>
        </table>

        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:20px;">
            <a href="tel:{{ $lead->phone }}" class="btn btn-primary">Call Lead</a>

            <a href="https://wa.me/91{{ preg_replace('/\D/', '', $lead->phone) }}?text={{ urlencode('Hi ' . $lead->name . ', thank you for your enquiry. Our team will guide you for the course admission.') }}"
               target="_blank"
               class="btn btn-success">
                WhatsApp
            </a>

            <a href="{{ route('admin.leads.index') }}" class="btn btn-light">Back</a>
        </div>
    </div>

    <div class="card">
        <h3 style="margin-top:0;">Update Follow-up</h3>

        <form method="POST" action="{{ route('admin.leads.update', $lead) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="new" {{ $lead->status === 'new' ? 'selected' : '' }}>New</option>
                    <option value="contacted" {{ $lead->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                    <option value="interested" {{ $lead->status === 'interested' ? 'selected' : '' }}>Interested</option>
                    <option value="not_interested" {{ $lead->status === 'not_interested' ? 'selected' : '' }}>Not Interested</option>
                    <option value="converted" {{ $lead->status === 'converted' ? 'selected' : '' }}>Converted</option>
                    <option value="lost" {{ $lead->status === 'lost' ? 'selected' : '' }}>Lost</option>
                </select>
            </div>

            <div class="form-group">
                <label>Next Follow-up Date</label>
                <input type="date" name="follow_up_date" value="{{ $lead->follow_up_date }}">
            </div>

            <div class="form-group">
                <label>Admin Notes</label>
                <textarea name="admin_notes" placeholder="Write call notes or follow-up remarks">{{ $lead->admin_notes }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;">
                Update Lead
            </button>
        </form>
    </div>
</div>

@endsection