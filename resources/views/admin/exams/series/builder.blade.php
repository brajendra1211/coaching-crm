@extends('admin.layouts.app')

@section('title', 'Series Builder')
@section('page_title', 'Series Builder')

@section('content')
<style>
    .builder-grid { display:grid; grid-template-columns:310px minmax(0,1fr); gap:18px; align-items:start; }
    .builder-panel { background:#fff; border:1px solid #e5e7eb; border-radius:18px; padding:16px; }
    .exam-row { display:grid; grid-template-columns:auto minmax(0,1fr) 170px; gap:12px; padding:12px; border:1px solid #e5e7eb; border-radius:14px; margin-bottom:10px; align-items:start; }
    .exam-row:hover { background:#f8fafc; }
    .exam-row input[type="checkbox"] { width:auto; margin-top:5px; }
    .meta { display:flex; gap:7px; flex-wrap:wrap; margin-top:7px; }
    .tag { display:inline-flex; padding:5px 8px; border-radius:999px; background:#eff6ff; color:#1d4ed8; font-size:12px; font-weight:900; }
    .builder-summary { position:sticky; top:96px; display:grid; gap:10px; }
    @media(max-width:900px){ .builder-grid{grid-template-columns:1fr;} .builder-summary{position:static;} }
</style>

@php
    $selected = $examSeries->exams->pluck('pivot.unlock_rule', 'id')->toArray();
@endphp

<div class="card">
    <div class="card-header">
        <div>
            <h3>{{ $examSeries->title }}</h3>
            <p style="margin:6px 0 0;color:#64748b;">Reusable exams select karke paid/free test series build karein.</p>
        </div>
        <a href="{{ route('admin.exam-series.index') }}" class="btn btn-light">Back</a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.exam-series.builder.update', $examSeries) }}">
        @csrf
        @method('PUT')

        <div class="builder-grid">
            <aside class="builder-panel builder-summary">
                <strong>Series Summary</strong>
                <div style="color:#64748b;font-size:14px;line-height:1.7;">
                    <div>Label: {{ $examSeries->label ?: ucfirst($examSeries->difficulty) }}</div>
                    <div>Access: {{ ucfirst($examSeries->access_type) }} {{ $examSeries->access_type === 'paid' ? '(Rs. ' . number_format((float) $examSeries->price, 2) . ')' : '' }}</div>
                    <div>Selected exams: {{ count($selected) }}</div>
                </div>
                <button class="btn btn-primary" type="submit" style="width:100%;">Save Series</button>
            </aside>

            <section>
                @forelse($exams as $exam)
                    <label class="exam-row">
                        <input type="checkbox" name="exam_ids[]" value="{{ $exam->id }}" {{ array_key_exists($exam->id, $selected) ? 'checked' : '' }}>
                        <span>
                            <strong>{{ $exam->title }}</strong>
                            <span class="meta">
                                <span class="tag">{{ $exam->label ?: ucfirst($exam->difficulty) }}</span>
                                <span class="tag">{{ ucfirst($exam->access_type) }}</span>
                                <span class="tag">{{ $exam->total_marks }} marks</span>
                                <span class="tag">{{ $exam->duration_minutes }} min</span>
                            </span>
                        </span>
                        <select name="unlock_rules[{{ $exam->id }}]">
                            <option value="always" {{ ($selected[$exam->id] ?? 'always') === 'always' ? 'selected' : '' }}>Always Open</option>
                            <option value="after_previous" {{ ($selected[$exam->id] ?? '') === 'after_previous' ? 'selected' : '' }}>After Previous</option>
                            <option value="scheduled_only" {{ ($selected[$exam->id] ?? '') === 'scheduled_only' ? 'selected' : '' }}>Scheduled Only</option>
                        </select>
                    </label>
                @empty
                    <div style="text-align:center;padding:35px;color:#64748b;">No exams found. Create single exams first.</div>
                @endforelse
            </section>
        </div>
    </form>
</div>
@endsection
