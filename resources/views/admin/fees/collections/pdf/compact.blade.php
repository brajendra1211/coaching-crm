@php
    $accent = $invoiceSetting->accent_color ?: '#2563eb';

    $instituteName = optional($setting)->institute_name ?? config('app.name');
    $address = optional($setting)->address;
    $phone = optional($setting)->phone;
    $email = optional($setting)->email;
    $logo = optional($setting)->logo;

    $logoPath = $logo ? public_path('storage/' . $logo) : null;
    $showLogo = ($invoiceSetting->show_logo ?? true) && $logoPath && file_exists($logoPath);

    $invoiceTitle = $invoiceSetting->invoice_title ?: 'Fee Receipt';
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $payment->receipt_no }}</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 10.5px;
            line-height: 1.35;
            background: #ffffff;
        }

        .invoice {
            padding: 14px;
        }

        .header {
            border-bottom: 3px solid {{ $accent }};
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-left {
            width: 66%;
            vertical-align: top;
        }

        .header-right {
            width: 34%;
            vertical-align: top;
            text-align: right;
        }

        .brand-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 48px;
            vertical-align: top;
        }

        .logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border: 1px solid #e5e7eb;
            padding: 3px;
        }

        h1, h2, h3, p {
            margin: 0;
        }

        .institute-name {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 2px;
        }

        .institute-info {
            color: #4b5563;
            font-size: 9.5px;
            line-height: 1.35;
        }

        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            color: {{ $accent }};
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .receipt-meta {
            color: #374151;
            font-size: 9.5px;
            line-height: 1.45;
        }

        .paid-label {
            display: inline-block;
            margin-top: 4px;
            padding: 3px 7px;
            border-radius: 20px;
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
            font-size: 9px;
            font-weight: bold;
        }

        .quick-info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 9px;
        }

        .quick-info td {
            border: 1px solid #e5e7eb;
            padding: 6px 7px;
            vertical-align: top;
            width: 33.33%;
        }

        .label {
            display: block;
            color: #6b7280;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .value {
            color: #111827;
            font-size: 10px;
            font-weight: bold;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid #e5e7eb;
            padding: 6px 7px;
        }

        .summary-table th {
            background: #f9fafb;
            text-align: left;
            color: #374151;
            font-size: 9px;
            text-transform: uppercase;
        }

        .summary-table td:last-child,
        .summary-table th:last-child {
            text-align: right;
            width: 130px;
        }

        .received-row td {
            font-weight: bold;
            background: #eff6ff;
            color: {{ $accent }};
            font-size: 11px;
        }

        .balance-row td {
            font-weight: bold;
            background: #f9fafb;
        }

        .note-box {
            margin-top: 8px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            padding: 7px;
            font-size: 9.5px;
            color: #374151;
        }

        .terms {
            margin-top: 8px;
            font-size: 9px;
            color: #4b5563;
            line-height: 1.4;
        }

        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 26px;
        }

        .bottom-table td {
            width: 50%;
            vertical-align: bottom;
        }

        .signature {
            width: 160px;
            border-top: 1px solid #111827;
            padding-top: 5px;
            text-align: center;
            font-size: 9.5px;
            font-weight: bold;
        }

        .right {
            text-align: right;
        }

        .right .signature {
            margin-left: auto;
        }

        .footer {
            margin-top: 16px;
            padding-top: 7px;
            border-top: 1px dashed #d1d5db;
            text-align: center;
            color: #6b7280;
            font-size: 9px;
        }
    </style>
</head>

<body>
    <div class="invoice">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="header-left">
                        <table class="brand-table">
                            <tr>
                                @if($showLogo)
                                    <td class="logo-cell">
                                        <img src="{{ $logoPath }}" class="logo" alt="Logo">
                                    </td>
                                @endif

                                <td>
                                    <div class="institute-name">{{ $instituteName }}</div>

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
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td class="header-right">
                        <div class="invoice-title">{{ $invoiceTitle }}</div>
                        <div class="receipt-meta">
                            Receipt: <strong>{{ $payment->receipt_no }}</strong><br>
                            Date: {{ $payment->payment_date ? $payment->payment_date->format('d M Y') : '-' }}<br>
                            Mode: {{ ucwords(str_replace('_', ' ', $payment->payment_mode)) }}
                        </div>

                        <span class="paid-label">PAID</span>
                    </td>
                </tr>
            </table>
        </div>

        <table class="quick-info">
            <tr>
                <td>
                    <span class="label">Student</span>
                    <span class="value">{{ optional($payment->student)->name ?: '-' }}</span>
                </td>

                <td>
                    <span class="label">Student Code</span>
                    <span class="value">{{ optional($payment->student)->student_code ?: '-' }}</span>
                </td>

                <td>
                    <span class="label">Phone</span>
                    <span class="value">{{ optional($payment->student)->phone ?: '-' }}</span>
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">Batch</span>
                    <span class="value">{{ optional($payment->batch)->name ?: '-' }}</span>
                </td>

                <td>
                    <span class="label">Fee Plan</span>
                    <span class="value">{{ optional(optional($payment->assignment)->feePlan)->title ?: '-' }}</span>
                </td>

                <td>
                    <span class="label">Transaction</span>
                    <span class="value">{{ $payment->transaction_id ?: '-' }}</span>
                </td>
            </tr>
        </table>

        <table class="summary-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>Total Fee</td>
                    <td>Rs. {{ number_format($payment->total_before_payment, 2) }}</td>
                </tr>

                <tr>
                    <td>Balance Before Payment</td>
                    <td>Rs. {{ number_format($payment->balance_before_payment, 2) }}</td>
                </tr>

                <tr>
                    <td>Fine Added</td>
                    <td>Rs. {{ number_format($payment->fine_amount, 2) }}</td>
                </tr>

                <tr>
                    <td>Discount Given</td>
                    <td>Rs. {{ number_format($payment->discount_amount, 2) }}</td>
                </tr>

                <tr class="received-row">
                    <td>Received Amount</td>
                    <td>Rs. {{ number_format($payment->amount, 2) }}</td>
                </tr>

                @if($invoiceSetting->show_balance ?? true)
                    <tr class="balance-row">
                        <td>Balance After Payment</td>
                        <td>Rs. {{ number_format($payment->balance_after_payment, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        @if($payment->notes)
            <div class="note-box">
                <strong>Notes:</strong> {{ $payment->notes }}
            </div>
        @endif

        @if($invoiceSetting->terms)
            <div class="terms">
                <strong>Terms:</strong> {{ $invoiceSetting->terms }}
            </div>
        @endif

        @if($invoiceSetting->show_signature ?? true)
            <table class="bottom-table">
                <tr>
                    <td>
                        <div class="signature">Student / Parent Signature</div>
                    </td>

                    <td class="right">
                        <div class="signature">
                            {{ $invoiceSetting->authorized_signature_label ?: 'Authorized Signature' }}
                        </div>
                    </td>
                </tr>
            </table>
        @endif

        @if($invoiceSetting->footer_note)
            <div class="footer">
                {{ $invoiceSetting->footer_note }}
            </div>
        @endif
    </div>
</body>
</html>