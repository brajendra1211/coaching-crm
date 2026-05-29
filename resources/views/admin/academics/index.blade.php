@extends('admin.layouts.app')

@section('title', $title)
@section('page_title', $title)

@section('content')

<style>
    .academic-toolbar {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 12px;
        margin-bottom: 18px;
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

    .status-active,
    .status-scheduled,
    .status-present {
        background: #dcfce7;
        color: #166534;
        border-color: #bbf7d0;
    }

    .status-inactive,
    .status-cancelled,
    .status-absent {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .status-completed,
    .status-late,
    .status-leave {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    .action-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    .table-value-muted {
        color: #64748b;
        font-size: 13px;
    }

    .link-text {
        color: #2563eb;
        font-weight: 900;
        word-break: break-all;
    }

    @media(max-width: 768px) {
        .academic-toolbar {
            grid-template-columns: 1fr;
        }

        .action-row .btn {
            width: auto;
        }
    }
</style>

@php
    $singleTitleMap = [
        'Teachers' => 'Teacher',
        'Subjects' => 'Subject',
        'Batches' => 'Batch',
        'Online Classes' => 'Online Class',
        'Study Materials' => 'Study Material',
    ];

    $singleTitle = $singleTitleMap[$title] ?? rtrim($title, 's');
@endphp

<div class="card">
    <div class="card-header">
        <div>
            <h3>{{ $title }}</h3>
            <p style="margin:6px 0 0;color:#64748b;">
                {{ $description ?? 'Manage records from this section.' }}
            </p>
        </div>

        <a href="{{ route('admin.' . $routePrefix . '.create') }}" class="btn btn-primary">
            + Add {{ $singleTitle }}
        </a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;padding:12px 14px;border-radius:12px;margin-bottom:15px;">
            <strong>Please fix the following errors:</strong>
            <ul style="margin:8px 0 0;padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" class="academic-toolbar">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search {{ strtolower($title) }}..."
        >

        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    @foreach($columns as $key => $label)
                        <th>{{ $label }}</th>
                    @endforeach

                    <th width="300">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($items as $item)
                    <tr>
                        @foreach($columns as $key => $label)
                            <td>
                                @php
                                    $value = $item->$key ?? null;
                                @endphp

                                @if($key === 'status')
                                    <span class="status-pill status-{{ $value }}">
                                        {{ ucwords(str_replace('_', ' ', $value ?: 'inactive')) }}
                                    </span>

                                @elseif($key === 'external_link' && $value)
                                    <a href="{{ $value }}" target="_blank" rel="noopener" class="link-text">
                                        Open Link
                                    </a>

                                @elseif(str_contains($key, 'date') && $value)
                                    {{ \Illuminate\Support\Carbon::parse($value)->format('d M Y') }}

                                @elseif(str_contains($key, 'time') && $value)
                                    {{ \Illuminate\Support\Carbon::parse($value)->format('h:i A') }}

                                @else
                                    {{ $value ?: '-' }}
                                @endif
                            </td>
                        @endforeach

                        <td>
                            <div class="action-row">
                                @if($routePrefix === 'batches')
                                    <a href="{{ route('admin.batches.show', $item) }}" class="btn btn-light">
                                        View
                                    </a>

                                    <a href="{{ route('admin.batches.builder', $item) }}" class="btn btn-dark">
                                        Builder
                                    </a>
                                @endif

                                <a href="{{ route('admin.' . $routePrefix . '.edit', $item) }}" class="btn btn-primary">
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.' . $routePrefix . '.destroy', $item) }}"
                                    onsubmit="return confirm('Are you sure you want to delete this record?')"
                                >
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
                        <td colspan="{{ count($columns) + 1 }}" style="text-align:center;padding:35px;color:#64748b;">
                            No records found.
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