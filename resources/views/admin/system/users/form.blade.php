@extends('admin.layouts.app')

@section('title', $user->exists ? 'Edit User' : 'Add User')
@section('page_title', $user->exists ? 'Edit User' : 'Add User')

@section('content')
<style>
    .system-form { max-width:920px; }
    .grid-2 { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; }
    .form-group { margin-bottom:18px; }
    .form-group label { display:block; font-weight:900; color:#334155; margin-bottom:8px; }
    @media(max-width:800px){ .grid-2{grid-template-columns:1fr;} }
</style>

@if($errors->any())
    <div style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;padding:14px;border-radius:14px;margin-bottom:18px;">
        <strong>Please fix errors:</strong>
        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" class="system-form">
    @csrf
    @if($user->exists) @method('PUT') @endif
    <div class="card">
        <div class="card-header"><h3>{{ $user->exists ? 'Edit User' : 'Add User' }}</h3></div>
        <div class="grid-2">
            <div class="form-group"><label>Name</label><input name="name" value="{{ old('name', $user->name) }}" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" value="{{ old('email', $user->email) }}" required></div>
            <div class="form-group"><label>Phone</label><input name="phone" value="{{ old('phone', $user->phone) }}"></div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" required>
                    @foreach(['super_admin' => 'Super Admin', 'admin' => 'Admin', 'staff' => 'Staff', 'teacher' => 'Teacher', 'student' => 'Student', 'parent' => 'Parent'] as $value => $label)
                        <option value="{{ $value }}" {{ old('role', $user->role) == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="form-group">
                <label>Link Student Profile</label>
                <select name="student_id">
                    <option value="">No Student Link</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ old('student_id', $user->student_id) == $student->id ? 'selected' : '' }}>
                            {{ $student->name }} {{ $student->student_code ? '(' . $student->student_code . ')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Link Teacher Profile</label>
                <select name="teacher_id">
                    <option value="">No Teacher Link</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('teacher_id', $user->teacher_id) == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }} {{ $teacher->email ? '(' . $teacher->email . ')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group"><label>Password {{ $user->exists ? '(leave blank to keep same)' : '' }}</label><input type="password" name="password" {{ $user->exists ? '' : 'required' }}></div>
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button class="btn btn-primary" type="submit">{{ $user->exists ? 'Update' : 'Save' }}</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-light">Cancel</a>
        </div>
    </div>
</form>
@endsection
