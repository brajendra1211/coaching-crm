@extends('admin.layouts.app')

@section('title', 'Custom Domains')
@section('page_title', 'Custom Domains')

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h3>Custom Domains</h3>
            <p style="margin:6px 0 0;color:#64748b;">Connect your coaching domain with DNS TXT verification.</p>
        </div>
    </div>

    @if(!$tenant)
        <div style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;padding:14px;border-radius:14px;">
            This admin is currently running on central/local domain. Custom domain settings work only after this coaching is opened through a tenant domain.
        </div>
    @else
        @if(session('success'))
            <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;padding:12px 14px;border-radius:12px;margin-bottom:15px;">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.domains.store') }}" style="display:grid;grid-template-columns:minmax(0,1fr) auto;gap:12px;margin-bottom:18px;">
            @csrf
            <input name="domain" placeholder="yourcoaching.com" required>
            <button class="btn btn-primary" type="submit">Add Domain</button>
        </form>

        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Domain</th><th>Status</th><th>DNS TXT Record</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($domains as $domain)
                        <tr>
                            <td>{{ $domain->domain }}</td>
                            <td>{{ ucfirst($domain->status) }}</td>
                            <td><code>{{ $domain->verification_token }}</code><br><small style="color:#64748b;">Add this as TXT record at your DNS provider.</small></td>
                            <td>
                                <form method="POST" action="{{ route('admin.domains.verify', $domain) }}">
                                    @csrf
                                    <button class="btn btn-light" type="submit">Verify</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;color:#64748b;">No domains added.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
