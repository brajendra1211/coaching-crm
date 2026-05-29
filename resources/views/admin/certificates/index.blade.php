@extends('admin.layouts.app')

@section('title', 'Certificates')
@section('page_title', 'Certificates')

@section('content')

<style>
    .certificate-toolbar {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 180px 160px auto;
        gap: 12px;
        margin-bottom: 18px;
    }

    .status-pill {
        display: inline-flex;
        padding: 7px 11px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        border: 1px solid;
        white-space: nowrap;
    }

    .status-active {
        background: #dcfce7;
        color: #166534;
        border-color: #bbf7d0;
    }

    .status-cancelled {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .action-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    @media(max-width: 950px) {
        .certificate-toolbar {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Certificates</h3>
            <p style="margin:6px 0 0;color:#64748b;">
                Create, preview and download student certificates.
            </p>
        </div>

        <a href="{{ route('admin.certificates.create') }}" class="btn btn-primary">
            + Create Certificate
        </a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" class="certificate-toolbar">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search certificate no, student, course..."
        >

        <select name="certificate_type">
            <option value="">All Types</option>
            <option value="completion" {{ request('certificate_type') === 'completion' ? 'selected' : '' }}>Completion</option>
            <option value="participation" {{ request('certificate_type') === 'participation' ? 'selected' : '' }}>Participation</option>
            <option value="achievement" {{ request('certificate_type') === 'achievement' ? 'selected' : '' }}>Achievement</option>
            <option value="training" {{ request('certificate_type') === 'training' ? 'selected' : '' }}>Training</option>
            <option value="appreciation" {{ request('certificate_type') === 'appreciation' ? 'selected' : '' }}>Appreciation</option>
            <option value="other" {{ request('certificate_type') === 'other' ? 'selected' : '' }}>Other</option>
        </select>

        <select name="status">
            <option value="">All Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>

        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Certificate</th>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Issue Date</th>
                    <th>Status</th>
                    <th width="300">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($certificates as $certificate)
                    <tr>
                        <td>
                            <strong>{{ $certificate->certificate_title }}</strong>
                            <br>
                            <small style="color:#64748b;">{{ $certificate->certificate_no }}</small>
                        </td>

                        <td>
                            {{ $certificate->recipient_name }}
                            <br>
                            <small style="color:#64748b;">{{ $certificate->student_code ?: '-' }}</small>
                        </td>

                        <td>
                            {{ $certificate->course_name ?: '-' }}
                            <br>
                            <small style="color:#64748b;">
                                {{ ucwords(str_replace('_', ' ', $certificate->certificate_type)) }}
                            </small>
                        </td>

                        <td>
                            {{ $certificate->issue_date ? $certificate->issue_date->format('d M Y') : '-' }}
                        </td>

                        <td>
                            <span class="status-pill status-{{ $certificate->status }}">
                                {{ ucfirst($certificate->status) }}
                            </span>
                        </td>

                        <td>
                            <div class="action-row">
                                <a href="{{ route('admin.certificates.show', $certificate) }}" class="btn btn-light">
                                    Preview
                                </a>

                                <a href="{{ route('admin.certificates.pdf', ['certificate' => $certificate->id, 'template' => $certificate->template]) }}" class="btn btn-primary">
                                    PDF
                                </a>

                                @if($certificate->status === 'active')
                                    <form method="POST" action="{{ route('admin.certificates.destroy', $certificate) }}" onsubmit="return confirm('Cancel this certificate?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-danger">Cancel</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:35px;color:#64748b;">
                            No certificates found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:22px;">
        {{ $certificates->links() }}
    </div>
</div>

@endsection