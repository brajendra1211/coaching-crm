@extends('admin.layouts.app')

@section('title', 'Courses')
@section('page_title', 'Course Management')

@section('content')

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:15px;flex-wrap:wrap;margin-bottom:18px;">
        <h3 style="margin:0;">All Courses</h3>
        <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">+ Add Course</a>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:900px;">
            <thead>
                <tr>
                    <th style="padding:12px;border-bottom:1px solid #e5e7eb;text-align:left;">Image</th>
                    <th style="padding:12px;border-bottom:1px solid #e5e7eb;text-align:left;">Course</th>
                    <th style="padding:12px;border-bottom:1px solid #e5e7eb;text-align:left;">Type</th>
                    <th style="padding:12px;border-bottom:1px solid #e5e7eb;text-align:left;">Fee</th>
                    <th style="padding:12px;border-bottom:1px solid #e5e7eb;text-align:left;">Status</th>
                    <th style="padding:12px;border-bottom:1px solid #e5e7eb;text-align:left;">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($courses as $course)
                    <tr>
                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                            @if($course->image)
                                <img src="{{ asset('storage/' . $course->image) }}" style="width:80px;height:55px;object-fit:cover;border-radius:10px;">
                            @else
                                No Image
                            @endif
                        </td>

                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                            <strong>{{ $course->title }}</strong><br>
                            <small>{{ $course->slug }}</small><br>
                            <small>{{ Str::limit($course->short_description, 70) }}</small>
                        </td>

                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                            {{ $course->course_type ?: 'N/A' }}<br>
                            <small>{{ $course->class_level ?: '' }}</small>
                        </td>

                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                            @if($course->offer_fee)
                                ₹{{ number_format($course->offer_fee, 0) }}
                                @if($course->fee)
                                    <small style="text-decoration:line-through;color:#999;">
                                        ₹{{ number_format($course->fee, 0) }}
                                    </small>
                                @endif
                            @elseif($course->fee)
                                ₹{{ number_format($course->fee, 0) }}
                            @else
                                N/A
                            @endif
                        </td>

                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                            <span style="padding:6px 10px;border-radius:999px;font-size:12px;font-weight:900;background:{{ $course->status === 'active' ? '#dcfce7' : '#fee2e2' }};color:{{ $course->status === 'active' ? '#166534' : '#991b1b' }};">
                                {{ ucfirst($course->status) }}
                            </span>
                        </td>

                        <td style="padding:12px;border-bottom:1px solid #e5e7eb;">
                            <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-dark">Edit</a>

                            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" style="display:inline-block;" onsubmit="return confirm('Delete this course?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-light">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding:20px;text-align:center;">No courses found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $courses->links() }}
    </div>
</div>

@endsection