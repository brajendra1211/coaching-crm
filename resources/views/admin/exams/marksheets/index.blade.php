@extends('admin.layouts.app')

@section('title', 'Marksheets')
@section('page_title', 'Marksheets')

@section('content')
<style>
    .toolbar { display:grid; grid-template-columns:260px auto; gap:12px; margin-bottom:18px; }
    .marksheet { border:1px solid #e5e7eb; border-radius:18px; padding:16px; background:#fff; margin-bottom:14px; display:grid; grid-template-columns:minmax(0,1fr) auto; gap:12px; align-items:center; }
    @media(max-width:800px){ .toolbar,.marksheet{grid-template-columns:1fr;} }
</style>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Marksheets</h3>
            <p style="margin:6px 0 0;color:#64748b;">Printable student performance cards from published results.</p>
        </div>
    </div>

    <form method="GET" class="toolbar">
        <select name="exam_id"><option value="">All Exams</option>@foreach($exams as $exam)<option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>{{ $exam->title }}</option>@endforeach</select>
        <button class="btn btn-primary" type="submit">Filter</button>
    </form>

    @forelse($results as $result)
        <div class="marksheet">
            <div>
                <h3 style="margin:0 0 6px;">{{ $result->student->name ?? '-' }}</h3>
                <p style="margin:0;color:#64748b;">{{ $result->exam->title ?? '-' }} | {{ $result->exam->subject->name ?? 'General' }} | {{ $result->exam->batch->name ?? 'All Batches' }}</p>
                <p style="margin:8px 0 0;"><strong>{{ $result->marks_obtained }}</strong> / {{ $result->total_marks }} marks, {{ $result->percentage }}%, Grade {{ $result->grade ?: '-' }}, Rank {{ $result->rank ?: '-' }}</p>
            </div>
            <button class="btn btn-light" type="button" onclick="window.print()">Print</button>
        </div>
    @empty
        <div style="text-align:center;padding:35px;color:#64748b;">No marksheets found.</div>
    @endforelse

    <div style="margin-top:22px;">{{ $results->links() }}</div>
</div>
@endsection
