@extends('admin.layouts.app')

@section('title', 'Blogs')
@section('page_title', 'Blog Management')

@section('content')

<style>
    .blog-toolbar {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 170px auto;
        gap: 12px;
        margin-bottom: 18px;
    }

    .blog-img {
        width: 80px;
        height: 56px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
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

    .status-draft {
        background: #fef3c7;
        color: #92400e;
        border-color: #fde68a;
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
        .blog-toolbar {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Blogs</h3>
            <p style="margin:6px 0 0;color:#64748b;">
                Manage website blogs, SEO articles, news and updates.
            </p>
        </div>

        <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">+ Add Blog</a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" class="blog-toolbar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search blog title, slug, category">

        <select name="status">
            <option value="">All Status</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>

        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Blog</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Published</th>
                    <th>Views</th>
                    <th width="260">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($blogs as $blog)
                    <tr>
                        <td>
                            @if($blog->featured_image)
                                <img src="{{ asset('storage/' . $blog->featured_image) }}" class="blog-img" alt="{{ $blog->title }}">
                            @else
                                <div class="blog-img"></div>
                            @endif
                        </td>

                        <td>
                            <strong>{{ $blog->title }}</strong>
                            <br>
                            <small style="color:#64748b;">/blogs/{{ $blog->slug }}</small>
                            <br>
                            <small style="color:#64748b;">{{ \Illuminate\Support\Str::limit($blog->excerpt ?: $blog->seo_description, 80) }}</small>
                        </td>

                        <td>{{ $blog->category ?: 'General' }}</td>

                        <td>
                            <span class="status-pill status-{{ $blog->status }}">
                                {{ ucfirst($blog->status) }}
                            </span>
                        </td>

                        <td>{{ $blog->published_at ? $blog->published_at->format('d M Y') : '-' }}</td>

                        <td>{{ $blog->views }}</td>

                        <td>
                            <div class="action-row">
                                @if($blog->status === 'active')
                                    <a href="{{ route('blogs.show', $blog->slug) }}" target="_blank" class="btn btn-light">View</a>
                                @endif

                                <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-primary">Edit</a>

                                <form method="POST" action="{{ route('admin.blogs.destroy', $blog) }}" onsubmit="return confirm('Delete this blog?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:35px;color:#64748b;">
                            No blogs found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:22px;">
        {{ $blogs->links() }}
    </div>
</div>

@endsection