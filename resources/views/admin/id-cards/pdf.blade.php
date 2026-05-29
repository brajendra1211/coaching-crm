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

    $isCompact = $template === 'compact';
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ID Card - {{ $cardData['student_code'] }}</title>

    <style>
        @page {
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            background: #ffffff;
            color: #111827;
        }

        .page {
            width: 100%;
            text-align: center;
        }

        .card-wrap {
            display: inline-block;
            width: {{ $isCompact ? '250px' : '290px' }};
            border: 1px solid #d1d5db;
            background: #ffffff;
            overflow: hidden;
        }

        .top {
            background: {{ $accent }};
            color: #ffffff;
            padding: {{ $isCompact ? '12px' : '16px' }};
            text-align: center;
        }

        .logo {
            width: {{ $isCompact ? '38px' : '48px' }};
            height: {{ $isCompact ? '38px' : '48px' }};
            object-fit: contain;
            background: #ffffff;
            padding: 4px;
            margin-bottom: 6px;
        }

        .top h3 {
            margin: 0;
            color: #ffffff;
            font-size: {{ $isCompact ? '13px' : '15px' }};
            line-height: 1.2;
        }

        .top p {
            margin: 4px 0 0;
            color: rgba(255,255,255,.86);
            font-size: {{ $isCompact ? '8.5px' : '9.5px' }};
        }

        .photo-box {
            width: {{ $isCompact ? '78px' : '92px' }};
            height: {{ $isCompact ? '78px' : '92px' }};
            border-radius: 999px;
            border: 4px solid #ffffff;
            background: #f1f5f9;
            margin: -28px auto 8px;
            overflow: hidden;
            text-align: center;
        }

        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-initial {
            width: 100%;
            height: 100%;
            background: {{ $accent }};
            color: #ffffff;
            font-size: {{ $isCompact ? '30px' : '36px' }};
            font-weight: bold;
            line-height: {{ $isCompact ? '78px' : '92px' }};
        }

        .student-name {
            text-align: center;
            padding: 0 14px;
        }

        .student-name h2 {
            margin: 0;
            color: #111827;
            font-size: {{ $isCompact ? '15px' : '18px' }};
            line-height: 1.2;
        }

        .student-name p {
            margin: 4px 0 8px;
            color: {{ $accent }};
            font-size: {{ $isCompact ? '9px' : '10px' }};
            font-weight: bold;
        }

        .details {
            padding: 0 {{ $isCompact ? '12px' : '16px' }} {{ $isCompact ? '12px' : '15px' }};
            text-align: left;
        }

        .detail-row {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-row td {
            padding: {{ $isCompact ? '4px 0' : '5px 0' }};
            vertical-align: top;
            font-size: {{ $isCompact ? '8.5px' : '9.2px' }};
        }

        .detail-row td:first-child {
            width: 75px;
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
        }

        .detail-row td:last-child {
            color: #111827;
            font-weight: bold;
        }

        .footer {
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
            padding: {{ $isCompact ? '7px 10px' : '9px 12px' }};
            color: #64748b;
            font-size: {{ $isCompact ? '7.6px' : '8.4px' }};
            line-height: 1.35;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="card-wrap">
            <div class="top">
                @if($logoDataUri)
                    <img src="{{ $logoDataUri }}" class="logo" alt="Logo">
                @endif

                <h3>{{ $cardData['institute_name'] }}</h3>
                <p>{{ $cardData['tagline'] }}</p>
            </div>

            <div class="photo-box">
                @if($studentPhotoDataUri)
                    <img src="{{ $studentPhotoDataUri }}" alt="{{ $cardData['student_name'] }}">
                @else
                    <div class="photo-initial">
                        {{ strtoupper(mb_substr($cardData['student_name'], 0, 1)) }}
                    </div>
                @endif
            </div>

            <div class="student-name">
                <h2>{{ $cardData['student_name'] }}</h2>
                <p>{{ $cardData['student_code'] }}</p>
            </div>

            <div class="details">
                <table class="detail-row">
                    <tr>
                        <td>Course</td>
                        <td>{{ $cardData['course_name'] }}</td>
                    </tr>
                </table>

                <table class="detail-row">
                    <tr>
                        <td>Class</td>
                        <td>{{ $cardData['class_level'] }}</td>
                    </tr>
                </table>

                <table class="detail-row">
                    <tr>
                        <td>Batch</td>
                        <td>{{ $cardData['batch_name'] }}</td>
                    </tr>
                </table>

                <table class="detail-row">
                    <tr>
                        <td>Phone</td>
                        <td>{{ $cardData['phone_number'] }}</td>
                    </tr>
                </table>

                <table class="detail-row">
                    <tr>
                        <td>Parent</td>
                        <td>{{ $cardData['parent_name'] }}</td>
                    </tr>
                </table>

                <table class="detail-row">
                    <tr>
                        <td>Status</td>
                        <td>{{ ucfirst($cardData['status']) }}</td>
                    </tr>
                </table>
            </div>

            <div class="footer">
                {{ $cardData['address'] ?: 'This card is property of the institute.' }}
            </div>
        </div>
    </div>
</body>
</html>