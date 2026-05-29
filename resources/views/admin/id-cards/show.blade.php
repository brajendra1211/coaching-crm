@extends('admin.layouts.app')

@section('title', 'ID Card Preview')
@section('page_title', 'ID Card Preview')

@section('content')

@php
    $accent = '#2563eb';

    if ($template === 'classic') {
        $accent = '#111827';
    }

    if ($template === 'minimal') {
        $accent = '#334155';
    }

    if ($template === 'compact') {
        $accent = '#0f766e';
    }

    $photoPath = $student->photo
        ?? $student->profile_photo
        ?? $student->image
        ?? $student->avatar
        ?? null;
@endphp

<style>
    .id-preview-wrap {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 320px;
        gap: 22px;
        align-items: start;
    }

    .toolbar-card,
    .preview-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 12px 35px rgba(15,23,42,.07);
        padding: 20px;
    }

    .template-form {
        display: grid;
        gap: 12px;
    }

    .id-card-stage {
        display: flex;
        justify-content: center;
        padding: 30px;
        background:
            radial-gradient(circle at top right, rgba(37,99,235,.08), transparent 30%),
            #f8fafc;
        border-radius: 22px;
        border: 1px solid #e5e7eb;
    }

    .student-id-card {
        width: 340px;
        min-height: 540px;
        background: #fff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 45px rgba(15,23,42,.16);
        border: 1px solid #e5e7eb;
        position: relative;
    }

    .id-top-band {
        height: 100px;
        background: linear-gradient(135deg, {{ $accent }}, #7c3aed);
        color: #fff;
        padding: 18px;
        text-align: center;
    }

    .id-top-band h3 {
        margin: 0;
        font-size: 18px;
        color: #fff;
    }

    .id-top-band p {
        margin: 5px 0 0;
        font-size: 12px;
        color: rgba(255,255,255,.88);
    }

    .student-photo {
        width: 112px;
        height: 112px;
        border-radius: 999px;
        background: #fff;
        border: 5px solid #fff;
        margin: -42px auto 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 12px 25px rgba(15,23,42,.18);
        overflow: hidden;
    }

    .student-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .student-photo span {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, {{ $accent }}, #7c3aed);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 44px;
        font-weight: 900;
    }

    .student-main {
        text-align: center;
        padding: 0 22px 14px;
    }

    .student-main h2 {
        margin: 0;
        color: #111827;
        font-size: 22px;
    }

    .student-main p {
        margin: 6px 0 0;
        color: {{ $accent }};
        font-weight: 900;
        font-size: 13px;
    }

    .id-details {
        padding: 0 22px 20px;
        display: grid;
        gap: 9px;
    }

    .detail-row {
        display: grid;
        grid-template-columns: 100px minmax(0, 1fr);
        gap: 8px;
        border-bottom: 1px dashed #e5e7eb;
        padding-bottom: 8px;
    }

    .detail-row small {
        color: #64748b;
        font-weight: 900;
        text-transform: uppercase;
        font-size: 10px;
    }

    .detail-row strong {
        color: #111827;
        font-size: 13px;
        word-break: break-word;
    }

    .id-footer {
        background: #f8fafc;
        border-top: 1px solid #e5e7eb;
        padding: 12px 18px;
        text-align: center;
        color: #64748b;
        font-size: 11px;
        line-height: 1.5;
    }

    @media(max-width: 900px) {
        .id-preview-wrap {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="id-preview-wrap">
    <main class="preview-card">
        <div class="card-header">
            <div>
                <h3>ID Card Preview</h3>
                <p style="margin:6px 0 0;color:#64748b;">
                    Preview selected student ID card before PDF download.
                </p>
            </div>
        </div>

        <div class="id-card-stage">
            <div class="student-id-card">
                <div class="id-top-band">
                    <h3>{{ $cardData['institute_name'] }}</h3>
                    <p>{{ $cardData['tagline'] }}</p>
                </div>

                <div class="student-photo">
                    @if($photoPath)
                        <img src="{{ asset('storage/' . $photoPath) }}" alt="{{ $cardData['student_name'] }}">
                    @else
                        <span>{{ strtoupper(mb_substr($cardData['student_name'], 0, 1)) }}</span>
                    @endif
                </div>

                <div class="student-main">
                    <h2>{{ $cardData['student_name'] }}</h2>
                    <p>{{ $cardData['student_code'] }}</p>
                </div>

                <div class="id-details">
                    <div class="detail-row">
                        <small>Course</small>
                        <strong>{{ $cardData['course_name'] }}</strong>
                    </div>

                    <div class="detail-row">
                        <small>Class</small>
                        <strong>{{ $cardData['class_level'] }}</strong>
                    </div>

                    <div class="detail-row">
                        <small>Batch</small>
                        <strong>{{ $cardData['batch_name'] }}</strong>
                    </div>

                    <div class="detail-row">
                        <small>Phone</small>
                        <strong>{{ $cardData['phone_number'] }}</strong>
                    </div>

                    <div class="detail-row">
                        <small>Parent</small>
                        <strong>{{ $cardData['parent_name'] }}</strong>
                    </div>

                    <div class="detail-row">
                        <small>Status</small>
                        <strong>{{ ucfirst($cardData['status']) }}</strong>
                    </div>
                </div>

                <div class="id-footer">
                    {{ $cardData['address'] ?: 'This card is property of the institute.' }}
                </div>
            </div>
        </div>
    </main>

    <aside class="toolbar-card">
        <h3 style="margin-top:0;">Download Options</h3>

        <form method="GET" action="{{ route('admin.id-cards.show', $student) }}" class="template-form">
            <label style="font-weight:900;color:#334155;">Template</label>

            <select name="template">
                <option value="premium" {{ $template === 'premium' ? 'selected' : '' }}>Premium</option>
                <option value="classic" {{ $template === 'classic' ? 'selected' : '' }}>Classic</option>
                <option value="minimal" {{ $template === 'minimal' ? 'selected' : '' }}>Minimal</option>
                <option value="compact" {{ $template === 'compact' ? 'selected' : '' }}>Compact</option>
            </select>

            <button type="submit" class="btn btn-light">Preview Template</button>

            <a href="{{ route('admin.id-cards.pdf', ['student' => $student->id, 'template' => $template]) }}" class="btn btn-primary">
                Download PDF
            </a>

            <a href="{{ route('admin.id-cards.index') }}" class="btn btn-light">
                Back to ID Cards
            </a>
        </form>

        <div style="margin-top:18px;background:#f8fafc;border:1px solid #e5e7eb;border-radius:16px;padding:14px;color:#64748b;font-size:13px;line-height:1.7;">
            Multiple templates will not make the system heavy. Only selected template is rendered when PDF is generated.
        </div>
    </aside>
</div>

@endsection