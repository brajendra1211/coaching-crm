@extends('admin.layouts.app')

@section('title', 'SEO Manager')
@section('page_title', 'SEO Manager')

@section('content')

<style>
    .seo-toolbar {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 170px auto;
        gap: 12px;
        margin-bottom: 18px;
    }

    .seo-path {
        display: inline-flex;
        padding: 7px 10px;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 900;
    }

    .status-pill {
        display: inline-flex;
        padding: 7px 11px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        border: 1px solid;
    }

    .status-active {
        background: #dcfce7;
        color: #166534;
        border-color: #bbf7d0;
    }

    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .action-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    @media(max-width: 800px) {
        .seo-toolbar {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="card">
    <div class="card-header">
        <div>
            <h3>SEO Manager</h3>
            <p style="margin:6px 0 0;color:#64748b;">
                Manage meta title, description, robots, OG image and schema for website URLs.
            </p>
        </div>

        <a href="{{ route('admin.seo.create') }}" class="btn btn-primary">+ Add SEO Meta</a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" class="seo-toolbar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search page, path, title, description">

        <select name="status">
            <option value="">All Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>

        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Page</th>
                    <th>Path</th>
                    <th>Meta Title</th>
                    <th>Robots</th>
                    <th>Status</th>
                    <th width="260">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($seoMetas as $seoMeta)
                    <tr>
                        <td>
                            <strong>{{ $seoMeta->page_name ?: 'Untitled Page' }}</strong>
                            <br>
                            <small style="color:#64748b;">
                                {{ \Illuminate\Support\Str::limit($seoMeta->meta_description, 75) }}
                            </small>
                        </td>

                        <td>
                            <span class="seo-path">{{ $seoMeta->path }}</span>
                        </td>

                        <td>
                            {{ \Illuminate\Support\Str::limit($seoMeta->meta_title, 70) ?: '-' }}
                        </td>

                        <td>
                            <small style="color:#475569;">{{ $seoMeta->robots }}</small>
                        </td>

                        <td>
                            <span class="status-pill status-{{ $seoMeta->status }}">
                                {{ ucfirst($seoMeta->status) }}
                            </span>
                        </td>

                        <td>
                            <div class="action-row">
                                <a href="{{ url($seoMeta->path) }}" target="_blank" class="btn btn-light">View</a>
                                <a href="{{ route('admin.seo.edit', $seoMeta) }}" class="btn btn-primary">Edit</a>

                                <form method="POST" action="{{ route('admin.seo.destroy', $seoMeta) }}" onsubmit="return confirm('Delete this SEO meta?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:35px;color:#64748b;">
                            No SEO meta records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:22px;">
        {{ $seoMetas->links() }}
    </div>
</div>

@endsection