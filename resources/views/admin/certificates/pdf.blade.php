@php
    $template = $template ?? 'premium';

    // Premium default colors
    $accent = '#b8860b';
    $accentDark = '#7c5800';
    $accentSoft = '#fff8e1';
    $navy = '#0f172a';
    $navyLight = '#1e293b';
    $paper = '#fffdf7';

    if ($template === 'classic') {
        $accent = '#111827';
        $accentDark = '#030712';
        $accentSoft = '#f3f4f6';
        $navy = '#111827';
        $navyLight = '#374151';
        $paper = '#ffffff';
    }

    if ($template === 'minimal') {
        $accent = '#334155';
        $accentDark = '#0f172a';
        $accentSoft = '#f8fafc';
        $navy = '#0f172a';
        $navyLight = '#334155';
        $paper = '#ffffff';
    }

    $instituteName = optional($setting)->institute_name ?? config('app.name');
    $tagline = optional($setting)->tagline;
    $address = optional($setting)->address;
    $phone = optional($setting)->phone;
    $email = optional($setting)->email;

    $certificateNo = $certificate->certificate_no ?: 'CERT-0001';
    $certificateTitle = $certificate->certificate_title ?: 'Certificate of Achievement';
    $recipientName = $certificate->recipient_name ?: 'Student Name';
    $courseName = $certificate->course_name ?: 'the program';

    $issueDate = $certificate->issue_date
        ? \Carbon\Carbon::parse($certificate->issue_date)->format('d M Y')
        : now()->format('d M Y');

    $completionDate = $certificate->completion_date
        ? \Carbon\Carbon::parse($certificate->completion_date)->format('d M Y')
        : '-';

    $initial = mb_strtoupper(mb_substr($instituteName, 0, 1));
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $certificateNo }}</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            color: #111827;
            font-family: DejaVu Sans, sans-serif;
        }

        body {
            width: 194mm;
            min-height: 281mm;
            text-align: center;
        }

        .page {
            width: 100%;
            padding-top: 9mm;
            text-align: center;
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        .print-title {
            margin-bottom: 7px;
            color: #64748b;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.8px;
        }

        .certificate-card {
            width: 84mm;
            height: 128mm;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
            background: {{ $paper }};
            border-radius: 16px;
            border: 1px solid #d6c084;
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        .inner-border {
            position: absolute;
            left: 5px;
            right: 5px;
            top: 5px;
            bottom: 5px;
            border: 1px solid {{ $accent }};
            border-radius: 12px;
            z-index: 2;
        }

        .thin-border {
            position: absolute;
            left: 9px;
            right: 9px;
            top: 9px;
            bottom: 9px;
            border: 1px solid #ead9a1;
            border-radius: 9px;
            z-index: 2;
        }

        .corner {
            position: absolute;
            width: 20px;
            height: 20px;
            z-index: 4;
        }

        .corner.tl {
            left: 11px;
            top: 11px;
            border-top: 2px solid {{ $accent }};
            border-left: 2px solid {{ $accent }};
        }

        .corner.tr {
            right: 11px;
            top: 11px;
            border-top: 2px solid {{ $accent }};
            border-right: 2px solid {{ $accent }};
        }

        .corner.bl {
            left: 11px;
            bottom: 11px;
            border-bottom: 2px solid {{ $accent }};
            border-left: 2px solid {{ $accent }};
        }

        .corner.br {
            right: 11px;
            bottom: 11px;
            border-bottom: 2px solid {{ $accent }};
            border-right: 2px solid {{ $accent }};
        }

        .top-header {
            position: relative;
            z-index: 5;
            background: {{ $navy }};
            color: #ffffff;
            padding: 11px 12px 13px;
            text-align: center;
            border-bottom: 3px solid {{ $accent }};
        }

        .gold-line {
            position: absolute;
            left: 14px;
            right: 14px;
            bottom: -7px;
            height: 3px;
            background: {{ $accent }};
            border-radius: 20px;
        }

        .logo-circle {
            width: 41px;
            height: 41px;
            margin: 0 auto 6px;
            border-radius: 50%;
            background: #ffffff;
            border: 2px solid {{ $accent }};
            padding: 4px;
            overflow: hidden;
            text-align: center;
        }

        .logo {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .logo-fallback {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: {{ $accentSoft }};
            color: {{ $accentDark }};
            font-size: 20px;
            font-weight: bold;
            line-height: 29px;
        }

        .institute-name {
            margin: 0;
            color: #ffffff;
            font-size: 12.5px;
            line-height: 1.15;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .2px;
        }

        .tagline {
            margin-top: 3px;
            color: #e5e7eb;
            font-size: 6.2px;
            line-height: 1.25;
            text-transform: uppercase;
            letter-spacing: .7px;
        }

        .badge {
            display: inline-block;
            margin-top: 7px;
            padding: 3px 10px;
            border-radius: 999px;
            background: {{ $accent }};
            color: #ffffff;
            font-size: 6.2px;
            line-height: 1;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .8px;
        }

        .watermark {
            position: absolute;
            z-index: 1;
            top: 54mm;
            left: 0;
            right: 0;
            text-align: center;
            color: {{ $accent }};
            opacity: .07;
            font-size: 26px;
            line-height: 1;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .body {
            position: relative;
            z-index: 5;
            padding: 12px 13px 0;
            text-align: center;
        }

        .cert-no {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 999px;
            background: {{ $accentSoft }};
            border: 1px solid #ead9a1;
            color: {{ $accentDark }};
            font-size: 6.3px;
            font-weight: bold;
            margin-bottom: 7px;
        }

        .ornament {
            width: 45mm;
            margin: 0 auto 5px;
            border-top: 1px solid #ead9a1;
            position: relative;
        }

        .ornament-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: {{ $accent }};
            margin: -4px auto 0;
        }

        .main-title {
            margin: 5px 0 0;
            color: {{ $navy }};
            font-size: 14px;
            line-height: 1.12;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .subtitle {
            margin-top: 3px;
            color: {{ $accentDark }};
            font-size: 6.2px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        .presented {
            margin: 8px 0 0;
            color: #64748b;
            font-size: 7px;
        }

        .recipient-box {
            margin: 6px auto 5px;
            padding: 5px 5px 4px;
            background: #ffffff;
            border-top: 1px solid #e7d599;
            border-bottom: 1px solid #e7d599;
        }

        .recipient {
            color: {{ $navy }};
            font-family: Georgia, DejaVu Serif, serif;
            font-size: 17px;
            line-height: 1.12;
            font-weight: bold;
        }

        .description {
            margin: 5px auto 0;
            color: #475569;
            font-size: 7.15px;
            line-height: 1.38;
            width: 100%;
        }

        .description strong {
            color: {{ $navy }};
        }

        .remark {
            margin: 4px auto 0;
            color: #64748b;
            font-size: 6.35px;
            line-height: 1.28;
            width: 96%;
        }

        .meta-table {
            width: 100%;
            margin-top: 8px;
            border-collapse: separate;
            border-spacing: 3px;
        }

        .meta-table td {
            width: 50%;
            padding: 4px 3px;
            text-align: center;
            vertical-align: top;
            background: #ffffff;
            border: 1px solid #ead9a1;
            border-radius: 6px;
        }

        .meta-table span {
            display: block;
            color: #7c5800;
            font-size: 5.35px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .45px;
            margin-bottom: 1px;
        }

        .meta-table strong {
            display: block;
            color: #111827;
            font-size: 6.35px;
            line-height: 1.15;
        }

        .bottom-area {
            width: 100%;
            margin-top: 7px;
            border-collapse: collapse;
        }

        .bottom-area td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
        }

        .verify-box {
            width: 38px;
            height: 38px;
            margin: 0 auto;
            border: 1px solid #d9c179;
            background: #ffffff;
            padding: 3px;
        }

        .verify-inner {
            width: 100%;
            height: 100%;
            border: 1px dashed {{ $accent }};
            color: {{ $accentDark }};
            font-size: 5.2px;
            line-height: 1.2;
            font-weight: bold;
            text-transform: uppercase;
            padding-top: 7px;
        }

        .premium-seal {
            width: 42px;
            height: 42px;
            margin: 0 auto;
            border-radius: 50%;
            border: 2px solid {{ $accent }};
            background: {{ $accentSoft }};
            color: {{ $accentDark }};
            font-size: 5.15px;
            line-height: 1.15;
            font-weight: bold;
            text-transform: uppercase;
            padding-top: 7px;
        }

        .signature {
            width: 74px;
            margin: 18px auto 0;
            border-top: 1px solid {{ $navy }};
            padding-top: 3px;
            color: {{ $navy }};
            font-size: 6.25px;
            font-weight: bold;
            line-height: 1.18;
        }

        .signature small {
            display: block;
            color: #64748b;
            font-size: 5.45px;
            font-weight: normal;
            margin-top: 1px;
        }

        .footer {
            position: absolute;
            z-index: 5;
            left: 10px;
            right: 10px;
            bottom: 9px;
            padding: 5px 7px 4px;
            border-top: 1px solid #ead9a1;
            color: #64748b;
            font-size: 5.45px;
            line-height: 1.25;
            text-align: center;
            background: rgba(255,255,255,.75);
        }

        .bottom-strip {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 5px;
            background: {{ $navy }};
            z-index: 6;
        }

        .cut-note {
            margin-top: 7px;
            color: #94a3b8;
            font-size: 7px;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="print-title">Premium Certificate Card</div>

        <div class="certificate-card">
            <div class="inner-border"></div>
            <div class="thin-border"></div>

            <div class="corner tl"></div>
            <div class="corner tr"></div>
            <div class="corner bl"></div>
            <div class="corner br"></div>

            <div class="top-header">
                <div class="logo-circle">
                    @if(!empty($logoDataUri))
                        <img src="{{ $logoDataUri }}" class="logo" alt="Logo">
                    @else
                        <div class="logo-fallback">{{ $initial }}</div>
                    @endif
                </div>

                <h2 class="institute-name">
                    {{ \Illuminate\Support\Str::limit($instituteName, 48) }}
                </h2>

                @if($tagline)
                    <div class="tagline">
                        {{ \Illuminate\Support\Str::limit($tagline, 60) }}
                    </div>
                @endif

                <div class="badge">Official Certificate</div>
                <div class="gold-line"></div>
            </div>

            <div class="watermark">Certified</div>

            <div class="body">
                <div class="cert-no">
                    Certificate No: {{ $certificateNo }}
                </div>

                <div class="ornament">
                    <div class="ornament-dot"></div>
                </div>

                <h1 class="main-title">
                    {{ \Illuminate\Support\Str::limit($certificateTitle, 44) }}
                </h1>

                <div class="subtitle">Certificate of Excellence</div>

                <div class="presented">
                    This certificate is proudly presented to
                </div>

                <div class="recipient-box">
                    <div class="recipient">
                        {{ \Illuminate\Support\Str::limit($recipientName, 32) }}
                    </div>
                </div>

                <div class="description">
                    for successfully completing / participating in
                    <strong>{{ \Illuminate\Support\Str::limit($courseName, 58) }}</strong>

                    @if($certificate->duration)
                        for <strong>{{ \Illuminate\Support\Str::limit($certificate->duration, 22) }}</strong>
                    @endif

                    with dedication, discipline and excellent performance.
                </div>

                @if($certificate->description)
                    <div class="remark">
                        {{ \Illuminate\Support\Str::limit($certificate->description, 78) }}
                    </div>
                @endif

                <table class="meta-table">
                    <tr>
                        <td>
                            <span>Student Code</span>
                            <strong>{{ $certificate->student_code ?: '-' }}</strong>
                        </td>

                        <td>
                            <span>Issue Date</span>
                            <strong>{{ $issueDate }}</strong>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <span>Completion</span>
                            <strong>{{ $completionDate }}</strong>
                        </td>

                        <td>
                            <span>Grade / Rank</span>
                            <strong>{{ $certificate->grade ?: '-' }}</strong>
                        </td>
                    </tr>
                </table>

                <table class="bottom-area">
                    <tr>
                        <td>
                            <div class="verify-box">
                                <div class="verify-inner">
                                    Verify<br>Online
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="premium-seal">
                                Premium<br>
                                Verified<br>
                                Award
                            </div>
                        </td>

                        <td>
                            <div class="signature">
                                {{ \Illuminate\Support\Str::limit($certificate->signed_by ?: 'Authorized Signature', 24) }}
                                <small>{{ \Illuminate\Support\Str::limit($certificate->signature_title ?: 'Director', 22) }}</small>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="footer">
                @if($address)
                    {{ \Illuminate\Support\Str::limit($address, 78) }}
                @endif

                @if($phone)
                    <br>Phone: {{ $phone }}
                @endif

                @if($email)
                    | {{ $email }}
                @endif
            </div>

            <div class="bottom-strip"></div>
        </div>

        <div class="cut-note">Print, cut and laminate this premium certificate card.</div>
    </div>
</body>
</html>