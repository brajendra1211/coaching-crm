@extends('admin.layouts.app')

@section('title', 'Certificate Preview')
@section('page_title', 'Certificate Preview')

@section('content')

@php
    $template = request('template', $certificate->template ?: 'university');

    $primary = '#102a56';
    $primary2 = '#163b73';
    $gold = '#c79a2b';
    $goldDark = '#8a6418';
    $goldSoft = '#fff7df';
    $paper = '#fffdf7';

    if ($template === 'premium') {
        $primary = '#0f172a';
        $primary2 = '#1e293b';
        $gold = '#d4a017';
        $goldDark = '#8a6418';
        $goldSoft = '#fff8e1';
        $paper = '#fffdf7';
    }

    if ($template === 'classic') {
        $primary = '#111827';
        $primary2 = '#374151';
        $gold = '#6b7280';
        $goldDark = '#374151';
        $goldSoft = '#f3f4f6';
        $paper = '#ffffff';
    }

    if ($template === 'minimal') {
        $primary = '#334155';
        $primary2 = '#475569';
        $gold = '#94a3b8';
        $goldDark = '#475569';
        $goldSoft = '#f8fafc';
        $paper = '#ffffff';
    }

    $instituteName = optional($setting ?? null)->institute_name ?? config('app.name');
    $tagline = optional($setting ?? null)->tagline ?? 'Excellence in Education & Professional Training';
    $address = optional($setting ?? null)->address;
    $phone = optional($setting ?? null)->phone;
    $email = optional($setting ?? null)->email;

    $issueDate = $certificate->issue_date
        ? $certificate->issue_date->format('d M Y')
        : now()->format('d M Y');

    $completionDate = $certificate->completion_date
        ? $certificate->completion_date->format('d M Y')
        : '-';

    $certificateNo = $certificate->certificate_no ?: 'CERT-0001';
    $certificateTitle = $certificate->certificate_title ?: 'Certificate of Achievement';
    $recipientName = $certificate->recipient_name ?: 'Student Name';
    $courseName = $certificate->course_name ?: 'the program';
@endphp

<style>
    .certificate-preview-wrap {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 330px;
        gap: 24px;
        align-items: start;
    }

    .preview-card,
    .side-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 24px;
        box-shadow: 0 16px 45px rgba(15, 23, 42, .08);
    }

    .preview-card {
        padding: 22px;
    }

    .side-card {
        padding: 22px;
        position: sticky;
        top: 90px;
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
    }

    .card-header h3,
    .side-card h3 {
        margin: 0;
        color: #0f172a;
        font-size: 21px;
        font-weight: 900;
    }

    .card-header p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 14px;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 13px;
        border-radius: 999px;
        background: {{ $goldSoft }};
        color: {{ $goldDark }};
        font-size: 12px;
        font-weight: 900;
        border: 1px solid rgba(199, 154, 43, .35);
        white-space: nowrap;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: {{ $gold }};
    }

    .certificate-stage {
        background:
            radial-gradient(circle at top left, rgba(199,154,43,.12), transparent 28%),
            radial-gradient(circle at bottom right, rgba(16,42,86,.12), transparent 30%),
            #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 24px;
        padding: 28px;
        overflow-x: auto;
    }

    .certificate-paper {
        width: 960px;
        min-height: 650px;
        margin: 0 auto;
        background: {{ $paper }};
        position: relative;
        overflow: hidden;
        border: 3px solid {{ $primary }};
        box-shadow: 0 24px 65px rgba(15, 23, 42, .16);
    }

    .top-band {
        position: absolute;
        inset: 0 0 auto 0;
        height: 82px;
        background: linear-gradient(135deg, {{ $primary }}, {{ $primary2 }});
        border-bottom: 6px solid {{ $gold }};
        z-index: 1;
    }

    .bottom-band {
        position: absolute;
        inset: auto 0 0 0;
        height: 42px;
        background: linear-gradient(135deg, {{ $primary2 }}, {{ $primary }});
        border-top: 4px solid {{ $gold }};
        z-index: 1;
    }

    .outer-border {
        position: absolute;
        inset: 30px;
        border: 3px solid {{ $gold }};
        z-index: 2;
        pointer-events: none;
    }

    .inner-border {
        position: absolute;
        inset: 43px;
        border: 1px solid rgba(16, 42, 86, .55);
        z-index: 2;
        pointer-events: none;
    }

    .corner {
        position: absolute;
        width: 70px;
        height: 70px;
        z-index: 4;
    }

    .corner.tl {
        top: 48px;
        left: 48px;
        border-top: 5px solid {{ $gold }};
        border-left: 5px solid {{ $gold }};
    }

    .corner.tr {
        top: 48px;
        right: 48px;
        border-top: 5px solid {{ $gold }};
        border-right: 5px solid {{ $gold }};
    }

    .corner.bl {
        bottom: 48px;
        left: 48px;
        border-bottom: 5px solid {{ $gold }};
        border-left: 5px solid {{ $gold }};
    }

    .corner.br {
        bottom: 48px;
        right: 48px;
        border-bottom: 5px solid {{ $gold }};
        border-right: 5px solid {{ $gold }};
    }

    .watermark {
        position: absolute;
        left: 70px;
        right: 70px;
        top: 270px;
        text-align: center;
        font-size: 72px;
        font-weight: 900;
        letter-spacing: 9px;
        color: {{ $gold }};
        opacity: .07;
        text-transform: uppercase;
        z-index: 1;
        transform: rotate(-8deg);
    }

    .certificate-inner {
        position: relative;
        z-index: 5;
        padding: 42px 76px 48px;
        text-align: center;
    }

    .cert-top {
        display: grid;
        grid-template-columns: 130px minmax(0, 1fr) 150px;
        gap: 18px;
        align-items: center;
        min-height: 90px;
        color: #ffffff;
    }

    .logo-seal {
        width: 82px;
        height: 82px;
        border-radius: 50%;
        margin: 0 auto;
        background: #ffffff;
        border: 4px solid {{ $gold }};
        display: flex;
        align-items: center;
        justify-content: center;
        color: {{ $primary }};
        font-size: 34px;
        font-weight: 900;
        box-shadow: 0 8px 22px rgba(0, 0, 0, .18);
    }

    .institute-name {
        margin: 0;
        color: #ffffff;
        font-size: 28px;
        line-height: 1.12;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .8px;
    }

    .tagline {
        margin-top: 6px;
        color: rgba(255, 255, 255, .86);
        font-size: 12px;
        line-height: 1.35;
        text-transform: uppercase;
        letter-spacing: 1.4px;
    }

    .cert-no-box {
        text-align: right;
        color: rgba(255, 255, 255, .86);
        font-size: 12px;
        font-weight: 800;
        line-height: 1.45;
    }

    .cert-no-box strong {
        display: block;
        color: {{ $goldSoft }};
        font-size: 14px;
        margin-top: 2px;
    }

    .academic-ribbon {
        display: inline-block;
        margin-top: 44px;
        padding: 9px 30px;
        border-radius: 999px;
        background: {{ $gold }};
        color: #ffffff;
        font-size: 13px;
        font-weight: 900;
        letter-spacing: 2px;
        text-transform: uppercase;
        box-shadow: 0 8px 20px rgba(199, 154, 43, .28);
    }

    .certificate-title {
        margin: 24px 0 0;
        color: {{ $primary }};
        font-family: Georgia, serif;
        font-size: 46px;
        line-height: 1.05;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }

    .title-line-wrap {
        width: 360px;
        margin: 14px auto 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .title-line {
        height: 2px;
        background: {{ $gold }};
        flex: 1;
    }

    .title-diamond {
        width: 10px;
        height: 10px;
        background: {{ $gold }};
        transform: rotate(45deg);
    }

    .presented-text {
        margin-top: 26px;
        color: #64748b;
        font-size: 15px;
        letter-spacing: .3px;
    }

    .recipient-name {
        display: inline-block;
        margin-top: 14px;
        padding: 0 50px 8px;
        color: {{ $primary }};
        font-family: Georgia, serif;
        font-size: 43px;
        line-height: 1.1;
        font-weight: 900;
        border-bottom: 3px solid {{ $gold }};
    }

    .description {
        width: 720px;
        margin: 24px auto 0;
        color: #334155;
        font-size: 16px;
        line-height: 1.8;
    }

    .description strong {
        color: {{ $primary }};
    }

    .remark {
        width: 660px;
        margin: 12px auto 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
    }

    .info-grid {
        width: 760px;
        margin: 28px auto 0;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
    }

    .info-item {
        background: #fffaf0;
        border: 1px solid rgba(199, 154, 43, .45);
        border-radius: 12px;
        padding: 9px 8px;
        text-align: center;
    }

    .info-item span {
        display: block;
        color: {{ $goldDark }};
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .7px;
        margin-bottom: 4px;
    }

    .info-item strong {
        display: block;
        color: {{ $primary }};
        font-size: 12px;
        line-height: 1.25;
    }

    .bottom-area {
        width: 790px;
        margin: 34px auto 0;
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        align-items: end;
        gap: 30px;
    }

    .verify-box {
        width: 150px;
        height: 82px;
        margin: 0 auto;
        border: 2px solid {{ $gold }};
        background: #ffffff;
        padding: 8px;
    }

    .verify-inner {
        width: 100%;
        height: 100%;
        border: 1px dashed {{ $primary }};
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: {{ $primary }};
        font-size: 11px;
        font-weight: 900;
        line-height: 1.35;
        text-transform: uppercase;
    }

    .official-seal {
        width: 118px;
        height: 118px;
        margin: 0 auto;
        border-radius: 50%;
        border: 5px solid {{ $gold }};
        background:
            radial-gradient(circle, {{ $goldSoft }} 0%, #ffffff 70%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: {{ $primary }};
        text-align: center;
        font-size: 12px;
        line-height: 1.28;
        font-weight: 900;
        text-transform: uppercase;
        box-shadow: inset 0 0 0 3px #fff, inset 0 0 0 5px {{ $gold }};
    }

    .signature-box {
        width: 230px;
        margin: 0 auto;
        border-top: 2px solid {{ $primary }};
        padding-top: 8px;
        color: {{ $primary }};
        font-size: 13px;
        font-weight: 900;
        line-height: 1.25;
    }

    .signature-box small {
        display: block;
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
        margin-top: 4px;
    }

    .certificate-footer {
        position: absolute;
        left: 80px;
        right: 80px;
        bottom: 13px;
        z-index: 6;
        color: rgba(255, 255, 255, .92);
        text-align: center;
        font-size: 11px;
        line-height: 1.4;
    }

    .download-title {
        margin: 0 0 5px;
        color: #0f172a;
        font-size: 20px;
        font-weight: 900;
    }

    .download-subtitle {
        margin: 0 0 18px;
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
    }

    .option-form {
        display: grid;
        gap: 14px;
    }

    .field-label {
        color: #334155;
        font-size: 13px;
        font-weight: 900;
    }

    .template-select {
        width: 100%;
        border: 1px solid #dbe3ef;
        border-radius: 14px;
        padding: 12px 13px;
        color: #0f172a;
        font-weight: 800;
        outline: none;
        background: #ffffff;
    }

    .template-select:focus {
        border-color: {{ $gold }};
        box-shadow: 0 0 0 4px rgba(199, 154, 43, .13);
    }

    .cert-mini-info {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 14px;
        margin: 4px 0;
    }

    .cert-mini-info div {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        color: #64748b;
        font-size: 12px;
        padding: 5px 0;
        border-bottom: 1px dashed #e5e7eb;
    }

    .cert-mini-info div:last-child {
        border-bottom: 0;
    }

    .cert-mini-info strong {
        color: #0f172a;
        text-align: right;
    }

    .action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 45px;
        border-radius: 14px;
        text-decoration: none;
        border: 0;
        cursor: pointer;
        font-weight: 900;
        font-size: 14px;
    }

    .btn-gold {
        background: linear-gradient(135deg, {{ $gold }}, {{ $goldDark }});
        color: #ffffff;
        box-shadow: 0 10px 24px rgba(199, 154, 43, .26);
    }

    .btn-blue {
        background: linear-gradient(135deg, {{ $primary }}, {{ $primary2 }});
        color: #ffffff;
        box-shadow: 0 10px 24px rgba(16, 42, 86, .22);
    }

    .btn-soft {
        background: #f8fafc;
        color: #334155;
        border: 1px solid #e5e7eb;
    }

    @media (max-width: 1100px) {
        .certificate-preview-wrap {
            grid-template-columns: 1fr;
        }

        .side-card {
            position: static;
        }
    }
</style>

@if(session('success'))
    <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:14px;margin-bottom:16px;font-weight:800;">
        {{ session('success') }}
    </div>
@endif

<div class="certificate-preview-wrap">
    <main class="preview-card">
        <div class="card-header">
            <div>
                <h3>Certificate Preview</h3>
                <p>
                    University-style premium certificate preview. PDF download ke liye right side option use karein.
                </p>
            </div>

            <div class="status-pill">
                <span class="status-dot"></span>
                {{ ucfirst($template) }} Template
            </div>
        </div>

        <div class="certificate-stage">
            <div class="certificate-paper">
                <div class="top-band"></div>
                <div class="bottom-band"></div>

                <div class="outer-border"></div>
                <div class="inner-border"></div>

                <div class="corner tl"></div>
                <div class="corner tr"></div>
                <div class="corner bl"></div>
                <div class="corner br"></div>

                <div class="watermark">Certificate</div>

                <div class="certificate-inner">
                    <div class="cert-top">
                        <div>
                            <div class="logo-seal">
                                {{ mb_strtoupper(mb_substr($instituteName, 0, 1)) }}
                            </div>
                        </div>

                        <div>
                            <h2 class="institute-name">
                                {{ \Illuminate\Support\Str::limit($instituteName, 70) }}
                            </h2>

                            <div class="tagline">
                                {{ \Illuminate\Support\Str::limit($tagline, 95) }}
                            </div>
                        </div>

                        <div class="cert-no-box">
                            Certificate No.
                            <strong>{{ $certificateNo }}</strong>
                        </div>
                    </div>

                    <div class="academic-ribbon">
                        Official Academic Certificate
                    </div>

                    <h1 class="certificate-title">
                        {{ \Illuminate\Support\Str::limit($certificateTitle, 48) }}
                    </h1>

                    <div class="title-line-wrap">
                        <div class="title-line"></div>
                        <div class="title-diamond"></div>
                        <div class="title-line"></div>
                    </div>

                    <div class="presented-text">
                        This certificate is proudly awarded to
                    </div>

                    <div class="recipient-name">
                        {{ \Illuminate\Support\Str::limit($recipientName, 42) }}
                    </div>

                    <div class="description">
                        for successfully completing / participating in
                        <strong>{{ \Illuminate\Support\Str::limit($courseName, 80) }}</strong>

                        @if($certificate->duration)
                            for <strong>{{ \Illuminate\Support\Str::limit($certificate->duration, 32) }}</strong>
                        @endif

                        with dedication, discipline and excellent performance.
                    </div>

                    @if($certificate->description)
                        <div class="remark">
                            {{ \Illuminate\Support\Str::limit($certificate->description, 150) }}
                        </div>
                    @endif

                    <div class="info-grid">
                        <div class="info-item">
                            <span>Student Code</span>
                            <strong>{{ $certificate->student_code ?: '-' }}</strong>
                        </div>

                        <div class="info-item">
                            <span>Issue Date</span>
                            <strong>{{ $issueDate }}</strong>
                        </div>

                        <div class="info-item">
                            <span>Completion</span>
                            <strong>{{ $completionDate }}</strong>
                        </div>

                        <div class="info-item">
                            <span>Grade / Rank</span>
                            <strong>{{ $certificate->grade ?: '-' }}</strong>
                        </div>
                    </div>

                    <div class="bottom-area">
                        <div>
                            <div class="verify-box">
                                <div class="verify-inner">
                                    Verified<br>
                                    Certificate<br>
                                    {{ $certificateNo }}
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="official-seal">
                                Official<br>
                                Academic<br>
                                Seal
                            </div>
                        </div>

                        <div>
                            <div class="signature-box">
                                {{ $certificate->signed_by ?: 'Authorized Signature' }}
                                <small>{{ $certificate->signature_title ?: 'Director / Principal' }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="certificate-footer">
                    @if($address)
                        {{ \Illuminate\Support\Str::limit($address, 140) }}
                    @endif

                    @if($phone)
                        &nbsp; | &nbsp; Phone: {{ $phone }}
                    @endif

                    @if($email)
                        &nbsp; | &nbsp; Email: {{ $email }}
                    @endif
                </div>
            </div>
        </div>
    </main>

    <aside class="side-card">
        <h3 class="download-title">Download Options</h3>
        <p class="download-subtitle">
            Template select karke preview update karein, phir final PDF download karein.
        </p>

        <form method="GET" action="{{ route('admin.certificates.show', $certificate) }}" class="option-form">
            <label class="field-label">Certificate Template</label>

            <select name="template" class="template-select">
                <option value="university" {{ $template === 'university' ? 'selected' : '' }}>University Premium</option>
                <option value="premium" {{ $template === 'premium' ? 'selected' : '' }}>Premium Gold</option>
                <option value="classic" {{ $template === 'classic' ? 'selected' : '' }}>Classic</option>
                <option value="minimal" {{ $template === 'minimal' ? 'selected' : '' }}>Minimal</option>
            </select>

            <div class="cert-mini-info">
                <div>
                    <span>Certificate No</span>
                    <strong>{{ $certificateNo }}</strong>
                </div>

                <div>
                    <span>Recipient</span>
                    <strong>{{ \Illuminate\Support\Str::limit($recipientName, 22) }}</strong>
                </div>

                <div>
                    <span>Issue Date</span>
                    <strong>{{ $issueDate }}</strong>
                </div>
            </div>

            <button type="submit" class="action-btn btn-soft">
                Preview Template
            </button>

            <a href="{{ route('admin.certificates.pdf', ['certificate' => $certificate->id, 'template' => $template]) }}" class="action-btn btn-gold">
                Download Premium PDF
            </a>

            <a href="{{ route('admin.certificates.index') }}" class="action-btn btn-blue">
                Back to Certificates
            </a>
        </form>
    </aside>
</div>

@endsection