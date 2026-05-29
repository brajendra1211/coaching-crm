@extends('admin.layouts.app')

@section('title', 'Gallery')
@section('page_title', 'Gallery Management')

@section('content')

<style>
    .gallery-toolbar {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 150px 170px 150px auto;
        gap: 12px;
        margin-bottom: 18px;
    }

    .gallery-thumb {
        width: 92px;
        height: 62px;
        border-radius: 14px;
        object-fit: cover;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
    }

    .type-pill,
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

    .type-image {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    .type-youtube {
        background: #fef2f2;
        color: #b91c1c;
        border-color: #fecaca;
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

    @media(max-width: 1000px) {
        .gallery-toolbar {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Gallery Items</h3>
            <p style="margin:6px 0 0;color:#64748b;">
                Manage website gallery images and YouTube videos.
            </p>
        </div>

        <a href="{{ route('admin.gallery.create') }}" class="btn btn-primary">+ Add Gallery Item</a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" class="gallery-toolbar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title, category">

        <select name="type">
            <option value="">All Type</option>
            <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Images</option>
            <option value="youtube" {{ request('type') === 'youtube' ? 'selected' : '' }}>YouTube</option>
        </select>

        <select name="category">
            <option value="">All Category</option>
            @foreach($categories as $category)
                <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                    {{ $category }}
                </option>
            @endforeach
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
                    <th>Preview</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Order</th>
                    <th>Featured</th>
                    <th width="240">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>
                            @if($item->thumbnail_url)
                                <img src="{{ $item->thumbnail_url }}" class="gallery-thumb" alt="{{ $item->title }}">
                            @else
                                <div class="gallery-thumb"></div>
                            @endif
                        </td>

                        <td>
                            <strong>{{ $item->title }}</strong>
                            @if($item->description)
                                <br>
                                <small style="color:#64748b;">
                                    {{ \Illuminate\Support\Str::limit($item->description, 70) }}
                                </small>
                            @endif
                        </td>

                        <td>
                            <span class="type-pill type-{{ $item->type }}">
                                {{ $item->type === 'youtube' ? 'YouTube' : 'Image' }}
                            </span>
                        </td>

                        <td>{{ $item->category ?: 'General' }}</td>

                        <td>
                            <span class="status-pill status-{{ $item->status }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>

                        <td>{{ $item->sort_order }}</td>

                        <td>{{ $item->is_featured ? 'Yes' : 'No' }}</td>

                        <td>
                            <div class="action-row">
                                <a href="{{ route('admin.gallery.edit', $item) }}" class="btn btn-primary">Edit</a>

                                <form method="POST" action="{{ route('admin.gallery.destroy', $item) }}" onsubmit="return confirm('Delete this gallery item?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:35px;color:#64748b;">
                            No gallery items found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:22px;">
        {{ $items->links() }}
    </div>
</div>

@endsection