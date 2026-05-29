@extends('admin.layouts.app')

@section('title', 'Users')
@section('page_title', 'Users')

@section('content')
<style>
    .toolbar { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:12px; margin-bottom:18px; }
    .pill { display:inline-flex; padding:7px 11px; border-radius:999px; font-size:12px; font-weight:900; border:1px solid #bfdbfe; background:#eff6ff; color:#1d4ed8; }
    .actions { display:flex; gap:8px; flex-wrap:wrap; }
    @media(max-width:800px){ .toolbar{grid-template-columns:1fr;} }
</style>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Users</h3>
            <p style="margin:6px 0 0;color:#64748b;">Manage admin, staff, teacher, student and parent access.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Add User</a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">{{ session('success') }}</div>
    @endif

    <form method="GET" class="toolbar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email or phone...">
        <button class="btn btn-primary" type="submit">Search</button>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th width="210">Action</th></tr></thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?: '-' }}</td>
                        <td><span class="pill">{{ ucfirst($user->role ?? 'staff') }}</span></td>
                        <td>{{ ucfirst($user->status ?? 'active') }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">Edit</a>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;padding:35px;color:#64748b;">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:22px;">{{ $users->links() }}</div>
</div>
@endsection
