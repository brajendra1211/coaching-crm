@extends('super-admin.layouts.app')

@section('title', $tenant->name)
@section('page_title', $tenant->name)
@section('page_subtitle', 'Tenant database, plan, status, domain verification and subscription')

@section('content')
@php
    $loginUrl = $tenant->loginUrl('admin');
    $teacherLoginUrl = $tenant->loginUrl('teacher');
    $studentLoginUrl = $tenant->loginUrl('student');
    $staffLoginUrl = $tenant->loginUrl('staff');
    $loginEmail = $tenant->owner_email ?: 'admin email not set';
    $loginPassword = 'Admin@123';
    $shareMessage = "Your coaching CRM is ready.\nAdmin Login: {$loginUrl}\nStaff Login: {$staffLoginUrl}\nTeacher Login: {$teacherLoginUrl}\nStudent Login: {$studentLoginUrl}\nAdmin Email: {$loginEmail}\nPassword: {$loginPassword}\nPlease change password after first login.";
    $whatsappPhone = preg_replace('/\D+/', '', (string) $tenant->owner_phone);
    $whatsappUrl = $whatsappPhone
        ? 'https://wa.me/' . $whatsappPhone . '?text=' . rawurlencode($shareMessage)
        : 'https://wa.me/?text=' . rawurlencode($shareMessage);
    $mailUrl = 'mailto:' . $tenant->owner_email . '?subject=' . rawurlencode('Your Coaching CRM Login Details') . '&body=' . rawurlencode($shareMessage);
@endphp

<div class="grid-4">
    <div class="card stat"><small>Status</small><strong>{{ ucfirst($tenant->status) }}</strong></div>
    <div class="card stat"><small>Plan</small><strong>{{ $tenant->plan->name ?? '-' }}</strong></div>
    <div class="card stat"><small>Database</small><strong style="font-size:18px;">{{ $tenant->database_name }}</strong></div>
    <div class="card stat"><small>Expiry</small><strong style="font-size:20px;">{{ $tenant->subscription_ends_at ? $tenant->subscription_ends_at->format('d M Y') : '-' }}</strong></div>
</div>

<section class="card" style="margin-bottom:18px;">
    <div class="card-header">
        <div>
            <h3>Coaching Login Details</h3>
            <p style="margin:6px 0 0;color:#64748b;">Share this with the coaching admin after provisioning.</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ $mailUrl }}" class="btn btn-light">Share Email</a>
            <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-primary">Share WhatsApp</a>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <tbody>
                <tr><th>Admin Login URL</th><td><a href="{{ $loginUrl }}" target="_blank" style="color:#2563eb;font-weight:900;">{{ $loginUrl }}</a></td></tr>
                <tr><th>Staff Login URL</th><td><a href="{{ $staffLoginUrl }}" target="_blank" style="color:#2563eb;font-weight:900;">{{ $staffLoginUrl }}</a></td></tr>
                <tr><th>Teacher Login URL</th><td><a href="{{ $teacherLoginUrl }}" target="_blank" style="color:#2563eb;font-weight:900;">{{ $teacherLoginUrl }}</a></td></tr>
                <tr><th>Student Login URL</th><td><a href="{{ $studentLoginUrl }}" target="_blank" style="color:#2563eb;font-weight:900;">{{ $studentLoginUrl }}</a></td></tr>
                <tr><th>Admin Email</th><td>{{ $loginEmail }}</td></tr>
                <tr><th>Default Password</th><td><code>{{ $loginPassword }}</code></td></tr>
            </tbody>
        </table>
    </div>
</section>

<div class="grid-2">
    <section class="card">
        <div class="card-header">
            <h3>Coaching Details</h3>
            <a href="{{ route('super-admin.tenants.edit', $tenant) }}" class="btn btn-primary">Edit</a>
        </div>
        <p><strong>Owner:</strong> {{ $tenant->owner_name ?: '-' }}</p>
        <p><strong>Email:</strong> {{ $tenant->owner_email ?: '-' }}</p>
        <p><strong>Phone:</strong> {{ $tenant->owner_phone ?: '-' }}</p>
        <p><strong>Storage:</strong> {{ $tenant->storage_path ?: 'tenants/' . $tenant->slug }}</p>
        <form method="POST" action="{{ route('super-admin.tenants.status', $tenant) }}" style="display:flex;gap:10px;align-items:end;flex-wrap:wrap;margin-top:16px;">
            @csrf @method('PUT')
            <div style="min-width:180px;"><label>Status</label><select name="status">@foreach(['active','inactive','suspended','expired'] as $status)<option value="{{ $status }}" {{ $tenant->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>@endforeach</select></div>
            <button class="btn btn-dark" type="submit">Update Status</button>
        </form>
        <form method="POST" action="{{ route('super-admin.tenants.provision', $tenant) }}" style="margin-top:12px;">
            @csrf
            <button class="btn btn-light" type="submit">Provision Database + Storage</button>
        </form>
        <p style="margin:8px 0 0;color:#64748b;font-size:13px;font-weight:700;">
            First provision can take a few minutes because it creates all CRM tables. If it was interrupted, click again and it will continue pending migrations.
        </p>
    </section>

    <section class="card">
        <h3>Subscription</h3>
        <form method="POST" action="{{ route('super-admin.tenants.subscription', $tenant) }}">
            @csrf
            <div class="grid-2" style="margin-bottom:0;">
                <div class="form-group"><label>Billing Cycle</label><select name="billing_cycle"><option value="monthly">Monthly</option><option value="yearly">Yearly</option><option value="custom">Custom</option></select></div>
                <div class="form-group"><label>Amount</label><input type="number" step="0.01" min="0" name="amount"></div>
                <div class="form-group"><label>Starts At</label><input type="date" name="starts_at" value="{{ now()->format('Y-m-d') }}"></div>
                <div class="form-group"><label>Ends At</label><input type="date" name="ends_at" required></div>
                <div class="form-group" style="grid-column:1/-1;"><label>Notes</label><textarea name="notes"></textarea></div>
            </div>
            <button class="btn btn-primary" type="submit">Extend Subscription</button>
        </form>
    </section>
</div>

<section class="card" style="margin-bottom:18px;">
    <div class="card-header"><h3>Domains</h3></div>
    <form method="POST" action="{{ route('super-admin.tenants.domains.store', $tenant) }}" style="display:grid;grid-template-columns:minmax(0,1fr) auto;gap:12px;margin-bottom:16px;">
        @csrf
        <input name="domain" placeholder="coaching-example.com" required>
        <button class="btn btn-primary" type="submit">Add Domain</button>
    </form>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Domain</th><th>Status</th><th>DNS TXT Record</th><th>Action</th></tr></thead>
            <tbody>
                @forelse($tenant->domains as $domain)
                    <tr>
                        <td>{{ $domain->domain }}</td>
                        <td><span class="pill {{ $domain->status === 'verified' ? 'green' : 'orange' }}">{{ ucfirst($domain->status) }}</span></td>
                        <td><code>{{ $domain->verification_token }}</code><br><small>Add this TXT record in DNS, then click verify.</small></td>
                        <td>
                            <form method="POST" action="{{ route('super-admin.domains.verify', $domain) }}">
                                @csrf
                                <button class="btn btn-light" type="submit">Verify DNS</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;color:#64748b;">No domains added.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="card">
    <div class="card-header"><h3>How Domain Connection Works</h3></div>
    <p style="color:#64748b;line-height:1.7;margin:0;">Har coaching apna domain DNS me app server ke IP ya CNAME par point karegi. Verification ke liye TXT record add karegi. TXT verify hone ke baad same codebase request host se tenant identify karega, us tenant ka database connection use karega, aur tenant-specific storage folder se files serve karega.</p>
</section>
@endsection
