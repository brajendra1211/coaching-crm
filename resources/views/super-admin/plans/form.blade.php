@extends('super-admin.layouts.app')

@section('title', $plan->exists ? 'Edit Plan' : 'Add Plan')
@section('page_title', $plan->exists ? 'Edit Plan' : 'Add Plan')

@section('content')
<form method="POST" action="{{ $plan->exists ? route('super-admin.plans.update', $plan) : route('super-admin.plans.store') }}" class="card">
    @csrf
    @if($plan->exists) @method('PUT') @endif
    <div class="grid-2">
        <div class="form-group"><label>Name</label><input name="name" value="{{ old('name', $plan->name) }}" required></div>
        <div class="form-group"><label>Code</label><input name="code" value="{{ old('code', $plan->code) }}" placeholder="starter, pro, enterprise"></div>
        <div class="form-group"><label>Monthly Price</label><input type="number" step="0.01" min="0" name="monthly_price" value="{{ old('monthly_price', $plan->monthly_price) }}"></div>
        <div class="form-group"><label>Yearly Price</label><input type="number" step="0.01" min="0" name="yearly_price" value="{{ old('yearly_price', $plan->yearly_price) }}"></div>
        <div class="form-group"><label>Student Limit</label><input type="number" min="1" name="student_limit" value="{{ old('student_limit', $plan->student_limit) }}"></div>
        <div class="form-group"><label>Staff Limit</label><input type="number" min="1" name="staff_limit" value="{{ old('staff_limit', $plan->staff_limit) }}"></div>
        <div class="form-group"><label>Storage Limit MB</label><input type="number" min="1" name="storage_limit_mb" value="{{ old('storage_limit_mb', $plan->storage_limit_mb) }}"></div>
        <div class="form-group"><label>Status</label><select name="status"><option value="active" {{ old('status', $plan->status) === 'active' ? 'selected' : '' }}>Active</option><option value="inactive" {{ old('status', $plan->status) === 'inactive' ? 'selected' : '' }}>Inactive</option></select></div>
        <div class="form-group" style="grid-column:1/-1;"><label>Features</label><textarea name="features">{{ old('features', $plan->features) }}</textarea></div>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;"><button class="btn btn-primary" type="submit">Save</button><a href="{{ route('super-admin.plans.index') }}" class="btn btn-light">Cancel</a></div>
</form>
@endsection
