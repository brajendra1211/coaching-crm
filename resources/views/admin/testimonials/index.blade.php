@extends('admin.layouts.app')

@section('title', 'Testimonials')
@section('page_title', 'Testimonial Management')

@section('content')

<style>
    .testimonial-toolbar {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 160px 160px auto;
        gap: 12px;
        margin-bottom: 18px;
    }

    .testimonial-avatar {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        object-fit: cover;
        border: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
    }

    .status-pill {
        display: inline-flex;
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

    .rating-stars {
        color: #f59e0b;
        letter-spacing: 1px;
        font-weight: 900;
    }

    .action-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    @media(max-width: 900px) {
        .testimonial-toolbar {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Testimonials</h3>
            <p style="margin:6px 0 0;color:#64748b;">
                Manage student/parent reviews, ratings and featured testimonials.
            </p>
        </div>

        <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">+ Add Testimonial</a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" class="testimonial-toolbar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, course, location, review">

        <select name="status">
            <option value="">All Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>

        <select name="featured">
            <option value="">All Featured</option>
            <option value="yes" {{ request('featured') === 'yes' ? 'selected' : '' }}>Featured</option>
            <option value="no" {{ request('featured') === 'no' ? 'selected' : '' }}>Not Featured</option>
        </select>

        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Review</th>
                    <th>Rating</th>
                    <th>Status</th>
                    <th>Featured</th>
                    <th>Order</th>
                    <th width="230">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($testimonials as $testimonial)
                    <tr>
                        <td>
                            <div style="display:flex;gap:12px;align-items:center;">
                                @if($testimonial->image)
                                    <img src="{{ asset('storage/' . $testimonial->image) }}" class="testimonial-avatar" alt="{{ $testimonial->name }}">
                                @else
                                    <div class="testimonial-avatar">{{ $testimonial->initials }}</div>
                                @endif

                                <div>
                                    <strong>{{ $testimonial->name }}</strong>
                                    <br>
                                    <small style="color:#64748b;">{{ $testimonial->designation ?: 'Student' }}</small>
                                    @if($testimonial->course_name)
                                        <br>
                                        <small style="color:#64748b;">{{ $testimonial->course_name }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td>
                            <span style="color:#475569;">
                                {{ \Illuminate\Support\Str::limit($testimonial->review, 90) }}
                            </span>
                        </td>

                        <td>
                            <span class="rating-stars">
                                {!! str_repeat('★', $testimonial->rating) !!}{!! str_repeat('☆', 5 - $testimonial->rating) !!}
                            </span>
                        </td>

                        <td>
                            <span class="status-pill status-{{ $testimonial->status }}">
                                {{ ucfirst($testimonial->status) }}
                            </span>
                        </td>

                        <td>{{ $testimonial->is_featured ? 'Yes' : 'No' }}</td>

                        <td>{{ $testimonial->sort_order }}</td>

                        <td>
                            <div class="action-row">
                                <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-primary">Edit</a>

                                <form method="POST" action="{{ route('admin.testimonials.destroy', $testimonial) }}" onsubmit="return confirm('Delete this testimonial?')">
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
                            No testimonials found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:22px;">
        {{ $testimonials->links() }}
    </div>
</div>

@endsection