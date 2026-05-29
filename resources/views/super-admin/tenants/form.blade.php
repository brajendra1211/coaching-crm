@extends('super-admin.layouts.app')

@section('title', $tenant->exists ? 'Edit Coaching' : 'New Coaching')
@section('page_title', $tenant->exists ? 'Edit Coaching' : 'New Coaching')

@section('content')
<form method="POST" action="{{ $tenant->exists ? route('super-admin.tenants.update', $tenant) : route('super-admin.tenants.store') }}" class="card">
    @csrf
    @if($tenant->exists) @method('PUT') @endif
    <div class="grid-2">
        <div class="form-group"><label>Coaching Name</label><input name="name" value="{{ old('name', $tenant->name) }}" required></div>
        <div class="form-group"><label>Slug</label><input name="slug" value="{{ old('slug', $tenant->slug) }}" placeholder="auto from name"></div>
        <div class="form-group"><label>Owner Name</label><input name="owner_name" value="{{ old('owner_name', $tenant->owner_name) }}"></div>
        <div class="form-group"><label>Owner Email</label><input type="email" name="owner_email" value="{{ old('owner_email', $tenant->owner_email) }}"></div>
        <div class="form-group"><label>Owner Phone</label><input name="owner_phone" value="{{ old('owner_phone', $tenant->owner_phone) }}"></div>
        <div class="form-group"><label>Plan</label><select name="plan_id"><option value="">No Plan</option>@foreach($plans as $plan)<option value="{{ $plan->id }}" {{ old('plan_id', $tenant->plan_id) == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>@endforeach</select></div>
        <div class="form-group"><label>Database Name</label><input name="database_name" value="{{ old('database_name', $tenant->database_name) }}" placeholder="auto from slug"></div>
        <div class="form-group"><label>Database Host</label><input name="database_host" value="{{ old('database_host', $tenant->database_host) }}" placeholder="default tenant DB host"></div>
        <div class="form-group"><label>Database Username</label><input name="database_username" value="{{ old('database_username', $tenant->database_username) }}" placeholder="default tenant DB user"></div>
        <div class="form-group"><label>Database Password</label><input name="database_password" value="{{ old('database_password', $tenant->database_password) }}" placeholder="default tenant DB password"></div>
        <div class="form-group"><label>Storage Path</label><input name="storage_path" value="{{ old('storage_path', $tenant->storage_path) }}" placeholder="tenants/coaching-slug"></div>
        <div class="form-group"><label>Status</label><select name="status">@foreach(['active','inactive','suspended','expired'] as $status)<option value="{{ $status }}" {{ old('status', $tenant->status) === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <div class="form-group"><label>Trial Ends At</label><input type="date" name="trial_ends_at" value="{{ old('trial_ends_at', $tenant->trial_ends_at ? $tenant->trial_ends_at->format('Y-m-d') : '') }}"></div>
        <div class="form-group"><label>Subscription Ends At</label><input type="date" name="subscription_ends_at" value="{{ old('subscription_ends_at', $tenant->subscription_ends_at ? $tenant->subscription_ends_at->format('Y-m-d') : '') }}"></div>
        @unless($tenant->exists)
            <div class="form-group" style="grid-column:1/-1;"><label>Primary Domain</label><input name="domain" value="{{ old('domain') }}" placeholder="example.com"></div>
        @endunless
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;"><button class="btn btn-primary" type="submit">Save</button><a href="{{ route('super-admin.tenants.index') }}" class="btn btn-light">Cancel</a></div>
</form>
@endsection
