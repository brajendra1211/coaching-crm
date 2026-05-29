@php
    $variant = $pdfVariant ?? $template ?? 'global';

    $accent = $invoiceSetting->accent_color ?: '#2563eb';

    if ($variant === 'premium') {
        $accent = $invoiceSetting->accent_color ?: '#1d4ed8';
    }

    if ($variant === 'letterhead') {
        $accent = $invoiceSetting->accent_color ?: '#0f766e';
    }

    if ($variant === 'minimal') {
        $accent = '#111827';
    }

    if ($variant === 'gst') {
        $accent = $invoiceSetting->accent_color ?: '#7c3aed';
    }

    if ($variant === 'compact-slip') {
        $accent = '#334155';
    }

    $isCompact = $variant === 'compact-slip';
    $isLetterhead = $variant === 'letterhead';
    $isPremium = $variant === 'premium';
    $isMinimal = $variant === 'minimal';
    $isGst = $variant === 'gst';

    $instituteName = optional($setting)->institute_name ?? config('app.name');
    $tagline = optional($setting)->tagline;
    $address = optional($setting)->address;
    $phone = optional($setting)->phone;
    $email = optional($setting)->email;
    $gstNumber = optional($setting)->gst_number ?? null;

    $invoiceTitle = $isGst ? 'Tax Invoice / Fee Receipt' : ($invoiceSetting->invoice_title ?: 'Fee Receipt');

    $student = $payment->student;
    $batch = $payment->batch;
    $assignment = $payment->assignment;
    $feePlan = optional($assignment)->feePlan;

    $showLogo = ($invoiceSetting->show_logo ?? true) && !empty($logoDataUri);

    $pageMargin = $isCompact ? '8mm 9mm' : '11mm 12mm';
    $bodyFont = $isCompact ? '9.6px' : '10.4px';
    $headerPadding = $isCompact ? '10px 12px' : '14px 16px';
    $contentPadding = $isCompact ? '10px 12px 12px' : '14px 16px 15px';
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $payment->receipt_no }}</title>

    <style>
        @page {
            margin: {{ $pageMargin }};
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #0f172a;
            font-size: {{ $bodyFont }};
            line-height: 1.32;
            background: #ffffff;
        }

        .invoice {
            width: 100%;
            border: 1px solid #d7dde8;
        }

        .top-band {
            height: {{ $isCompact ? '5px' : '8px' }};
            background: {{ $accent }};
        }

        .premium-band {
            height: {{ $isPremium ? '22px' : '0' }};
            background: {{ $isPremium ? 'linear-gradient(90deg,' . $accent . ',#7c3aed)' : 'transparent' }};
        }

        .header {
            padding: {{ $headerPadding }};
            border-bottom: 1px solid #d7dde8;
            background: {{ $isMinimal ? '#ffffff' : '#f8fafc' }};
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-left {
            width: 64%;
            vertical-align: top;
        }

        .header-right {
            width: 36%;
            vertical-align: top;
            text-align: right;
        }

        .brand-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-cell {
            width: {{ $isCompact ? '48px' : '60px' }};
            vertical-align: top;
        }

        .logo-box {
            width: {{ $isCompact ? '40px' : '50px' }};
            height: {{ $isCompact ? '40px' : '50px' }};
            border: 1px solid #cbd5e1;
            background: #ffffff;
            padding: 4px;
            text-align: center;
        }

        .logo {
            max-width: {{ $isCompact ? '32px' : '40px' }};
            max-height: {{ $isCompact ? '32px' : '40px' }};
        }

        .institute-name {
            font-size: {{ $isCompact ? '14px' : '17px' }};
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .tagline {
            font-size: 8.8px;
            color: {{ $accent }};
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: .35px;
        }

        .institute-info {
            font-size: {{ $isCompact ? '8.4px' : '9.1px' }};
            color: #475569;
            line-height: 1.38;
        }

        .invoice-title {
            font-size: {{ $isCompact ? '16px' : '21px' }};
            line-height: 1;
            color: {{ $accent }};
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 6px;
        }

        .invoice-meta {
            font-size: {{ $isCompact ? '8.5px' : '9px' }};
            color: #334155;
            line-height: 1.5;
        }

        .paid-stamp {
            display: inline-block;
            margin-top: 6px;
            padding: 3px 8px;
            color: #166534;
            border: 1px solid #16a34a;
            font-size: 8.1px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .35px;
        }

        .content {
            padding: {{ $contentPadding }};
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: {{ $isCompact ? '8px' : '11px' }};
        }

        .summary-table td {
            width: 33.33%;
            padding: {{ $isCompact ? '6px 7px' : '8px 9px' }};
            border: 1px solid #d7dde8;
            vertical-align: top;
            background: {{ $isPremium ? '#f8fafc' : '#ffffff' }};
        }

        .summary-label {
            display: block;
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: .3px;
            margin-bottom: 3px;
        }

        .summary-value {
            font-size: {{ $isCompact ? '11px' : '13px' }};
            color: #0f172a;
            font-weight: bold;
        }

        .summary-value.accent {
            color: {{ $accent }};
        }

        .section-row {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: {{ $isCompact ? '8px' : '11px' }};
        }

        .section-row td {
            width: 50%;
            vertical-align: top;
        }

        .section-row td:first-child { padding-right: 9px; }
        .section-row td:last-child { padding-left: 9px; }

        .section-title {
            font-size: 8.7px;
            color: #ffffff;
            background: {{ $accent }};
            padding: 4px 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .35px;
        }

        .info-box {
            border: 1px solid #d7dde8;
            border-top: 0;
            padding: {{ $isCompact ? '6px 7px' : '7px 8px' }};
            min-height: {{ $isCompact ? '56px' : '68px' }};
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .info-table .label {
            width: 85px;
            color: #64748b;
            font-size: 8.4px;
        }

        .info-table .value {
            color: #0f172a;
            font-size: {{ $isCompact ? '8.8px' : '9.4px' }};
            font-weight: bold;
        }

        .amount-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        .amount-table th {
            padding: {{ $isCompact ? '5px 7px' : '6px 8px' }};
            background: {{ $isMinimal ? '#111827' : $accent }};
            color: #ffffff;
            font-size: 8.6px;
            text-transform: uppercase;
            text-align: left;
            letter-spacing: .3px;
        }

        .amount-table th:last-child,
        .amount-table td:last-child {
            text-align: right;
            width: {{ $isCompact ? '120px' : '150px' }};
        }

        .amount-table td {
            padding: {{ $isCompact ? '5px 7px' : '6px 8px' }};
            border-left: 1px solid #d7dde8;
            border-right: 1px solid #d7dde8;
            border-bottom: 1px solid #d7dde8;
            font-size: {{ $isCompact ? '9px' : '9.5px' }};
        }

        .amount-table .muted td { color: #475569; }

        .amount-table .received td {
            background: #eff6ff;
            color: {{ $accent }};
            font-size: {{ $isCompact ? '9.8px' : '10.8px' }};
            font-weight: bold;
        }

        .amount-table .balance td {
            background: #f8fafc;
            font-weight: bold;
            color: #0f172a;
        }

        .payment-details {
            width: 100%;
            border-collapse: collapse;
            margin-top: {{ $isCompact ? '7px' : '9px' }};
        }

        .payment-details td {
            border: 1px solid #d7dde8;
            padding: {{ $isCompact ? '5px 7px' : '6px 8px' }};
            vertical-align: top;
        }

        .payment-details .label {
            display: block;
            color: #64748b;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .payment-details .value {
            color: #0f172a;
            font-weight: bold;
            font-size: {{ $isCompact ? '8.8px' : '9.2px' }};
        }

        .terms {
            margin-top: {{ $isCompact ? '7px' : '9px' }};
            padding: 6px 8px;
            border-left: 3px solid {{ $accent }};
            background: #f8fafc;
            font-size: {{ $isCompact ? '8px' : '8.4px' }};
            color: #475569;
            line-height: 1.36;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: {{ $isCompact ? '18px' : '24px' }};
        }

        .signature-table td {
            width: 50%;
            vertical-align: bottom;
        }

        .signature-line {
            width: {{ $isCompact ? '145px' : '170px' }};
            border-top: 1px solid #0f172a;
            padding-top: 4px;
            text-align: center;
            font-size: 8.7px;
            color: #0f172a;
            font-weight: bold;
        }

        .right { text-align: right; }
        .right .signature-line { margin-left: auto; }

        .footer {
            margin-top: {{ $isCompact ? '8px' : '12px' }};
            padding: 7px 10px;
            background: #f8fafc;
            border-top: 1px solid #d7dde8;
            text-align: center;
            color: #64748b;
            font-size: 8.2px;
        }

        .watermark {
            position: fixed;
            top: {{ $isCompact ? '260px' : '330px' }};
            left: 125px;
            font-size: {{ $isCompact ? '52px' : '70px' }};
            font-weight: bold;
            color: rgba(22, 101, 52, 0.04);
            transform: rotate(-22deg);
            z-index: -1;
        }
    </style>
</head>

<body>
<div class="watermark">PAID</div>

<div class="invoice">
    @if($isPremium)
        <div class="premium-band"></div>
    @else
        <div class="top-band"></div>
    @endif

    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-left">
                    <table class="brand-table">
                        <tr>
                            @if($showLogo)
                                <td class="logo-cell">
                                    <div class="logo-box">
                                        <img src="{{ $logoDataUri }}" class="logo" alt="Logo">
                                    </div>
                                </td>
                            @endif

                            <td>
                                <div class="institute-name">{{ $instituteName }}</div>

                                @if($tagline)
                                    <div class="tagline">{{ $tagline }}</div>
                                @endif

                                <div class="institute-info">
                                    @if(($invoiceSetting->show_address ?? true) && $address)
                                        {{ $address }}<br>
                                    @endif

                                    @if(($invoiceSetting->show_phone ?? true) && $phone)
                                        Phone: {{ $phone }}
                                    @endif

                                    @if(($invoiceSetting->show_email ?? true) && $email)
                                        @if(($invoiceSetting->show_phone ?? true) && $phone)
                                            |
                                        @endif
                                        Email: {{ $email }}
                                    @endif

                                    @if($isGst && $gstNumber)
                                        <br>GSTIN: {{ $gstNumber }}
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>

                <td class="header-right">
                    <div class="invoice-title">{{ $invoiceTitle }}</div>

                    <div class="invoice-meta">
                        <strong>Receipt No:</strong> {{ $payment->receipt_no }}<br>
                        <strong>Date:</strong> {{ $payment->payment_date ? $payment->payment_date->format('d M Y') : '-' }}<br>
                        <strong>Mode:</strong> {{ ucwords(str_replace('_', ' ', $payment->payment_mode)) }}
                    </div>

                    <span class="paid-stamp">Payment Received</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="content">
        <table class="summary-table">
            <tr>
                <td>
                    <span class="summary-label">Total Fee</span>
                    <span class="summary-value">Rs. {{ number_format($payment->total_before_payment, 2) }}</span>
                </td>

                <td>
                    <span class="summary-label">Amount Received</span>
                    <span class="summary-value accent">Rs. {{ number_format($payment->amount, 2) }}</span>
                </td>

                <td>
                    <span class="summary-label">Balance</span>
                    <span class="summary-value">Rs. {{ number_format($payment->balance_after_payment, 2) }}</span>
                </td>
            </tr>
        </table>

        <table class="section-row">
            <tr>
                <td>
                    <div class="section-title">Bill To</div>
                    <div class="info-box">
                        <table class="info-table">
                            <tr>
                                <td class="label">Student</td>
                                <td class="value">{{ optional($student)->name ?: '-' }}</td>
                            </tr>

                            <tr>
                                <td class="label">Code</td>
                                <td class="value">{{ optional($student)->student_code ?: '-' }}</td>
                            </tr>

                            <tr>
                                <td class="label">Phone</td>
                                <td class="value">{{ optional($student)->phone ?: '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </td>

                <td>
                    <div class="section-title">Academic Details</div>
                    <div class="info-box">
                        <table class="info-table">
                            <tr>
                                <td class="label">Batch</td>
                                <td class="value">{{ optional($batch)->name ?: '-' }}</td>
                            </tr>

                            <tr>
                                <td class="label">Fee Plan</td>
                                <td class="value">{{ optional($feePlan)->title ?: '-' }}</td>
                            </tr>

                            <tr>
                                <td class="label">Transaction</td>
                                <td class="value">{{ $payment->transaction_id ?: '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <table class="amount-table">
            <thead>
            <tr>
                <th>Payment Particulars</th>
                <th>Amount</th>
            </tr>
            </thead>

            <tbody>
            <tr>
                <td>Total Assigned Fee</td>
                <td>Rs. {{ number_format($payment->total_before_payment, 2) }}</td>
            </tr>

            <tr class="muted">
                <td>Previous Balance</td>
                <td>Rs. {{ number_format($payment->balance_before_payment, 2) }}</td>
            </tr>

            <tr class="muted">
                <td>Late Fine Added</td>
                <td>Rs. {{ number_format($payment->fine_amount, 2) }}</td>
            </tr>

            <tr class="muted">
                <td>Discount Given</td>
                <td>Rs. {{ number_format($payment->discount_amount, 2) }}</td>
            </tr>

            <tr class="received">
                <td>Amount Received</td>
                <td>Rs. {{ number_format($payment->amount, 2) }}</td>
            </tr>

            @if($invoiceSetting->show_balance ?? true)
                <tr class="balance">
                    <td>Balance After Payment</td>
                    <td>Rs. {{ number_format($payment->balance_after_payment, 2) }}</td>
                </tr>
            @endif
            </tbody>
        </table>

        <table class="payment-details">
            <tr>
                <td>
                    <span class="label">Payment Date</span>
                    <span class="value">{{ $payment->payment_date ? $payment->payment_date->format('d M Y') : '-' }}</span>
                </td>

                <td>
                    <span class="label">Payment Mode</span>
                    <span class="value">{{ ucwords(str_replace('_', ' ', $payment->payment_mode)) }}</span>
                </td>

                <td>
                    <span class="label">Receipt Status</span>
                    <span class="value">{{ ucfirst($payment->status) }}</span>
                </td>
            </tr>
        </table>

        @if($payment->notes)
            <div class="terms">
                <strong>Note:</strong> {{ $payment->notes }}
            </div>
        @endif

        @if(!empty($invoiceSetting->terms))
            <div class="terms">
                <strong>Terms:</strong> {{ \Illuminate\Support\Str::limit($invoiceSetting->terms, $isCompact ? 160 : 240) }}
            </div>
        @endif

        @if($invoiceSetting->show_signature ?? true)
            <table class="signature-table">
                <tr>
                    <td>
                        <div class="signature-line">Student / Parent Signature</div>
                    </td>

                    <td class="right">
                        <div class="signature-line">
                            {{ $invoiceSetting->authorized_signature_label ?: 'Authorized Signature' }}
                        </div>
                    </td>
                </tr>
            </table>
        @endif
    </div>

    @if($invoiceSetting->footer_note)
        <div class="footer">
            {{ \Illuminate\Support\Str::limit($invoiceSetting->footer_note, $isCompact ? 120 : 180) }}
        </div>
    @endif
</div>
</body>
</html>