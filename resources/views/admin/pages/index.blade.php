@extends('admin.layouts.app')

@section('title', 'Website Pages')
@section('page_title', 'Website Page Builder')

@section('content')

<style>
    .page-toolbar {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 180px 180px auto;
        gap: 12px;
        margin-bottom: 18px;
        align-items: center;
    }

    .page-title-cell strong {
        display: block;
        color: #111827;
        font-size: 15px;
        margin-bottom: 4px;
    }

    .page-title-cell small {
        display: block;
        color: #64748b;
        line-height: 1.4;
    }

    .page-url {
        color: #2563eb;
        font-weight: 800;
        word-break: break-all;
    }

    .page-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .page-actions form {
        display: inline-block;
        margin: 0;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
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

    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .form-enabled {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    .form-disabled {
        background: #f8fafc;
        color: #64748b;
        border-color: #e5e7eb;
    }

    .empty-state {
        text-align: center;
        padding: 45px 20px;
        color: #64748b;
    }

    .empty-state h3 {
        margin: 0 0 8px;
        color: #111827;
    }

    @media(max-width: 900px) {
        .page-toolbar {
            grid-template-columns: 1fr;
        }

        .page-actions {
            flex-direction: column;
        }

        .page-actions .btn {
            width: 100%;
        }
    }
</style>

<div class="card">
    <div class="card-header" style="display:flex;justify-content:space-between;gap:15px;align-items:center;flex-wrap:wrap;">
        <div>
            <h3 style="margin:0;">Website Pages</h3>
            <p style="margin:6px 0 0;color:#64748b;">
                Manage dynamic pages like About, Contact, Admission, Results and custom landing pages.
            </p>
        </div>

        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
            + Add New Page
        </a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:13px 16px;border-radius:14px;margin-bottom:18px;">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" class="page-toolbar">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search page by title, slug or heading"
        >

        <select name="page_type">
            <option value="">All Page Types</option>
            <option value="default" {{ request('page_type') === 'default' ? 'selected' : '' }}>Default</option>
            <option value="about" {{ request('page_type') === 'about' ? 'selected' : '' }}>About</option>
            <option value="courses" {{ request('page_type') === 'courses' ? 'selected' : '' }}>Courses</option>
            <option value="contact" {{ request('page_type') === 'contact' ? 'selected' : '' }}>Contact</option>
            <option value="admission" {{ request('page_type') === 'admission' ? 'selected' : '' }}>Admission</option>
            <option value="results" {{ request('page_type') === 'results' ? 'selected' : '' }}>Results</option>
            <option value="custom" {{ request('page_type') === 'custom' ? 'selected' : '' }}>Custom</option>
        </select>

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
                    <th>Type</th>
                    <th>URL</th>
                    <th>Form</th>
                    <th>Status</th>
                    <th>Order</th>
                    <th width="260">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($pages as $page)
                    <tr>
                        <td class="page-title-cell">
                            <strong>{{ $page->title }}</strong>
                            <small>{{ $page->hero_title ?: 'No hero title added' }}</small>
                            <small>{{ \Illuminate\Support\Str::limit($page->seo_title ?: $page->seo_description, 75) }}</small>
                        </td>

                        <td>{{ ucwords(str_replace('_', ' ', $page->page_type)) }}</td>

                        <td>
                            <a href="{{ url('/' . $page->slug) }}" target="_blank" class="page-url">
                                /{{ $page->slug }}
                            </a>
                        </td>

                        <td>
                            <span class="status-pill {{ $page->show_enquiry_form ? 'form-enabled' : 'form-disabled' }}">
                                {{ $page->show_enquiry_form ? 'Enabled' : 'Disabled' }}
                            </span>
                        </td>

                        <td>
                            <span class="status-pill {{ $page->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                {{ ucfirst($page->status) }}
                            </span>
                        </td>

                        <td>{{ $page->sort_order ?? 0 }}</td>

                        <td>
                            <div class="page-actions">
                                <a href="{{ url('/' . $page->slug) }}" target="_blank" class="btn btn-light">
                                    View
                                </a>

                                <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-primary">
                                    Edit
                                </a>

                                <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" onsubmit="return confirm('Delete this page?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <h3>No Website Pages Found</h3>
                                <p>Create About, Contact, Admission or custom landing pages from admin panel.</p>
                                <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
                                    Create First Page
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:18px;">
        {{ $pages->links() }}
    </div>
</div>

@endsection
