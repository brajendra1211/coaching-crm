@extends('admin.layouts.app')

@section('title', 'Roles')
@section('page_title', 'Roles')

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h3>Roles</h3>
            <p style="margin:6px 0 0;color:#64748b;">Current access groups used by admin users.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Add User</a>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Role</th><th>Total Users</th><th>Access</th></tr></thead>
            <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td>{{ ucfirst($role->role ?: 'staff') }}</td>
                        <td>{{ $role->total }}</td>
                        <td>Dashboard, CRM modules and assigned operational sections.</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center;padding:35px;color:#64748b;">No roles found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
