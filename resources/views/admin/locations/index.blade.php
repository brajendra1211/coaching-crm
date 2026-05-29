@extends('admin.layouts.app')

@section('title', 'SEO Locations')
@section('page_title', 'Location SEO Pages')

@section('content')

<div class="card">
    <div class="card-header">
        <div>
            <h3>Location Landing Pages</h3>
            <p style="margin:6px 0 0;color:#64748b;">
                Manage local SEO pages for coaching keywords and locations.
            </p>
        </div>

        <a href="{{ route('admin.locations.create') }}" class="btn btn-primary">
            + Add Location Page
        </a>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Page</th>
                    <th>Location</th>
                    <th>Keyword</th>
                    <th>Status</th>
                    <th>URL</th>
                    <th width="190">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($locations as $location)
                    <tr>
                        <td>
                            <strong>{{ $location->page_title }}</strong><br>
                            <small>{{ \Illuminate\Support\Str::limit($location->seo_title, 70) }}</small>
                        </td>

                        <td>{{ $location->location_name }}</td>

                        <td>{{ $location->focus_keyword ?: 'N/A' }}</td>

                        <td>
                            <span class="badge {{ $location->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                {{ ucfirst($location->status) }}
                            </span>
                        </td>

                        <td>
                            <a href="{{ url('/' . $location->slug) }}" target="_blank">
                                /{{ $location->slug }}
                            </a>
                        </td>

                        <td>
                            <a href="{{ route('admin.locations.edit', $location) }}" class="btn btn-primary">Edit</a>

                            <form method="POST" action="{{ route('admin.locations.destroy', $location) }}" class="action-form" onsubmit="return confirm('Delete this SEO page?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;">No location pages found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:18px;">
        {{ $locations->links() }}
    </div>
</div>

@endsection