@extends('frontend.layouts.app')

@php
    $setting = $siteSetting ?? \App\Models\CoachingSetting::current();
    $instituteName = $setting->institute_name ?: 'Edu Institute';
    $pageTitle = $page?->seo_title ?: ($page?->hero_title ?: ('Results - ' . $instituteName));
    $pageDescription = $page?->seo_description ?: ('Search exam results, marks, percentage, rank and grade published by ' . $instituteName . '.');
@endphp

@section('title', $pageTitle)
@section('meta_description', $pageDescription)
@section('meta_keywords', $page?->seo_keywords ?: 'exam results, coaching result, student marks, rank, grade')

@push('styles')
<style>
    .results-hero {
        background:
            radial-gradient(circle at 12% 18%, rgba(255,255,255,.18), transparent 28%),
            linear-gradient(135deg, #0f172a, var(--primary), var(--secondary));
        color: #fff;
        padding: 70px 0 94px;
    }

    .results-hero h1 {
        max-width: 820px;
        margin: 0;
        color: #fff;
        font-size: clamp(36px, 5vw, 58px);
        line-height: 1.08;
    }

    .results-hero p {
        max-width: 760px;
        margin: 18px 0 0;
        color: rgba(255,255,255,.9);
        font-size: 18px;
        line-height: 1.75;
    }

    .result-search-wrap {
        margin-top: -44px;
        position: relative;
        z-index: 5;
    }

    .result-search-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 28px;
        padding: 24px;
        box-shadow: var(--shadow);
    }

    .result-search-form {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(0, .9fr) auto;
        gap: 14px;
        align-items: end;
    }

    .result-card-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .result-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 22px;
        box-shadow: var(--shadow-soft);
    }

    .result-card-head {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: flex-start;
        border-bottom: 1px solid var(--border);
        padding-bottom: 14px;
        margin-bottom: 16px;
    }

    .result-card h3 {
        margin: 0 0 5px;
        color: var(--heading);
        font-size: 21px;
    }

    .result-card small {
        color: var(--muted);
        font-weight: 800;
        line-height: 1.5;
    }

    .result-status {
        padding: 8px 12px;
        border-radius: 999px;
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
    }

    .result-status.fail {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .result-metrics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
    }

    .result-metric {
        background: #f8fafc;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 12px;
    }

    .result-metric span {
        display: block;
        color: var(--muted);
        font-size: 12px;
        font-weight: 900;
        margin-bottom: 6px;
    }

    .result-metric strong {
        color: var(--heading);
        font-size: 18px;
        font-weight: 900;
    }

    .rank-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .rank-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 22px;
        padding: 18px;
        box-shadow: var(--shadow-soft);
    }

    .rank-badge {
        width: 44px;
        height: 44px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        margin-bottom: 12px;
    }

    .rank-card h3 {
        margin: 0 0 6px;
        color: var(--heading);
        font-size: 18px;
    }

    .rank-card p {
        margin: 0;
        color: var(--muted);
        line-height: 1.55;
        font-size: 14px;
    }

    .result-content {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 24px;
        box-shadow: var(--shadow-soft);
        color: var(--text);
        line-height: 1.8;
    }

    .empty-state {
        background: #fff;
        border: 1px dashed #bfdbfe;
        border-radius: 24px;
        padding: 30px;
        text-align: center;
        color: var(--muted);
        font-weight: 800;
    }

    @media(max-width: 980px) {
        .result-search-form,
        .result-card-grid,
        .rank-grid {
            grid-template-columns: 1fr;
        }

        .result-metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>
@endpush

@section('content')

<section class="results-hero">
    <div class="container">
        <span class="badge" style="background:rgba(255,255,255,.14);color:#fff;border-color:rgba(255,255,255,.25);">Published Results</span>
        <h1>{{ $page?->hero_title ?: 'Search student exam results and performance records' }}</h1>
        <p>{{ $page?->hero_subtitle ?: 'Students can search result by name, student code or phone number and view marks, percentage, grade and rank.' }}</p>
    </div>
</section>

<section class="result-search-wrap">
    <div class="container">
        <div class="result-search-card">
            <form method="GET" action="{{ route('results.index') }}" class="result-search-form">
                <div class="form-group" style="margin:0;">
                    <label>Search Student</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Name, student code or phone number">
                </div>

                <div class="form-group" style="margin:0;">
                    <label>Exam</label>
                    <select name="exam_id">
                        <option value="">All Exams</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}" @selected((string) request('exam_id') === (string) $exam->id)>
                                {{ $exam->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Search Result</button>
            </form>
        </div>
    </div>
</section>

<section class="section section-light">
    <div class="container">
        @if(request()->filled('q') || request()->filled('exam_id'))
            <div class="section-title">
                <h2>Search Results</h2>
                <p>Showing matching published result records.</p>
            </div>

            @if($results->count())
                <div class="result-card-grid">
                    @foreach($results as $result)
                        <article class="result-card">
                            <div class="result-card-head">
                                <div>
                                    <h3>{{ $result->student->name ?? 'Student' }}</h3>
                                    <small>
                                        {{ $result->student->student_code ?? 'No code' }} |
                                        {{ $result->exam->title ?? 'Exam' }}
                                    </small>
                                </div>

                                <span class="result-status {{ $result->result_status === 'fail' ? 'fail' : '' }}">
                                    {{ ucfirst($result->result_status) }}
                                </span>
                            </div>

                            <div class="result-metrics">
                                <div class="result-metric">
                                    <span>Marks</span>
                                    <strong>{{ $result->marks_obtained }} / {{ $result->total_marks }}</strong>
                                </div>
                                <div class="result-metric">
                                    <span>Percentage</span>
                                    <strong>{{ $result->percentage }}%</strong>
                                </div>
                                <div class="result-metric">
                                    <span>Grade</span>
                                    <strong>{{ $result->grade ?: '-' }}</strong>
                                </div>
                                <div class="result-metric">
                                    <span>Rank</span>
                                    <strong>{{ $result->rank ?: '-' }}</strong>
                                </div>
                            </div>

                            @if($result->remarks)
                                <p style="margin:16px 0 0;color:var(--muted);line-height:1.65;">{{ $result->remarks }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>

                <div style="margin-top:22px;">{{ $results->links() }}</div>
            @else
                <div class="empty-state">
                    No published result found. Please check student code, phone number or exam selection.
                </div>
            @endif
        @else
            @if($topResults->count())
                <div class="section-title">
                    <h2>Top Rankers</h2>
                    <p>Recently published rank-wise student performance.</p>
                </div>

                <div class="rank-grid">
                    @foreach($topResults as $result)
                        <article class="rank-card">
                            <span class="rank-badge">{{ $result->rank ?: '#' }}</span>
                            <h3>{{ $result->student->name ?? 'Student' }}</h3>
                            <p>{{ $result->exam->title ?? 'Exam' }}</p>
                            <p><strong>{{ $result->percentage }}%</strong> | Grade {{ $result->grade ?: '-' }}</p>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    Results will appear here after admin publishes exam results.
                </div>
            @endif
        @endif

        @if($page?->content)
            <div class="result-content" style="margin-top:34px;">
                {!! $page->content !!}
            </div>
        @endif
    </div>
</section>

@if($latestResults->count())
    <section class="section">
        <div class="container">
            <div class="section-title">
                <h2>Latest Published Results</h2>
                <p>Newest result records available for students.</p>
            </div>

            <div class="result-card-grid">
                @foreach($latestResults as $result)
                    <article class="result-card">
                        <div class="result-card-head">
                            <div>
                                <h3>{{ $result->student->name ?? 'Student' }}</h3>
                                <small>{{ $result->exam->title ?? 'Exam' }}</small>
                            </div>
                            <span class="result-status {{ $result->result_status === 'fail' ? 'fail' : '' }}">{{ ucfirst($result->result_status) }}</span>
                        </div>

                        <div class="result-metrics">
                            <div class="result-metric"><span>Marks</span><strong>{{ $result->marks_obtained }}/{{ $result->total_marks }}</strong></div>
                            <div class="result-metric"><span>Percent</span><strong>{{ $result->percentage }}%</strong></div>
                            <div class="result-metric"><span>Grade</span><strong>{{ $result->grade ?: '-' }}</strong></div>
                            <div class="result-metric"><span>Rank</span><strong>{{ $result->rank ?: '-' }}</strong></div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif

@endsection
