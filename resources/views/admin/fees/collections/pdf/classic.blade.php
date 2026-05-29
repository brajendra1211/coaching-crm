@php
    $accent = $invoiceSetting->accent_color ?: '#111827';

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
            font-size: 12px;
            line-height: 1.45;
            background: #ffffff;
        }

        .invoice {
            width: 100%;
            padding: 22px;
        }

        .top-border {
            height: 7px;
            background: {{ $accent }};
            margin-bottom: 18px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .header-left {
            width: 65%;
            vertical-align: top;
        }

        .header-right {
            width: 35%;
            vertical-align: top;
            text-align: right;
        }

        .brand-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 72px;
            vertical-align: top;
        }

        .logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border: 1px solid #e5e7eb;
            padding: 5px;
        }

        h1, h2, h3, p {
            margin: 0;
        }

        .institute-name {
            font-size: 22px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 4px;
        }

        .institute-info {
            color: #4b5563;
            font-size: 11px;
            line-height: 1.5;
        }

        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: {{ $accent }};
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .receipt-meta {
            display: inline-block;
            border: 1px solid #d1d5db;
            padding: 8px 10px;
            font-size: 11px;
            text-align: left;
        }

        .section-title {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 8px 10px;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            font-size: 11px;
            margin-top: 16px;
            margin-bottom: 0;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .info-table td {
            width: 50%;
            border: 1px solid #d1d5db;
            padding: 9px 10px;
            vertical-align: top;
        }

        .label {
            display: block;
            color: #6b7280;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .value {
            color: #111827;
            font-weight: bold;
            font-size: 12px;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }

        .payment-table th,
        .payment-table td {
            border: 1px solid #d1d5db;
            padding: 9px 10px;
        }

        .payment-table th {
            background: #f3f4f6;
            color: #111827;
            font-size: 11px;
            text-transform: uppercase;
            text-align: left;
        }

        .payment-table td:last-child,
        .payment-table th:last-child {
            text-align: right;
            width: 180px;
        }

        .total-row td {
            font-weight: bold;
            background: #f9fafb;
        }

        .received-row td {
            font-weight: bold;
            color: {{ $accent }};
            font-size: 13px;
            background: #f3f4f6;
        }

        .note-box {
            margin-top: 14px;
            border: 1px solid #d1d5db;
            padding: 10px;
            background: #f9fafb;
            font-size: 11px;
        }

        .terms {
            margin-top: 14px;
            font-size: 10.5px;
            color: #4b5563;
            border: 1px solid #e5e7eb;
            padding: 10px;
            background: #ffffff;
        }

        .signature-table {
            width: 100%;
            margin-top: 48px;
            border-collapse: collapse;
        }

        .signature-table td {
            width: 50%;
            vertical-align: bottom;
        }

        .signature-line {
            width: 210px;
            border-top: 1px solid #111827;
            padding-top: 7px;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            color: #111827;
        }

        .right-sign {
            text-align: right;
        }

        .right-sign .signature-line {
            margin-left: auto;
        }

        .footer-note {
            margin-top: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 10.5px;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }

        .status {
            display: inline-block;
            margin-top: 9px;
            padding: 5px 9px;
            border: 1px solid #16a34a;
            color: #166534;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div class="invoice">
        <div class="top-border"></div>

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
                        <strong>Receipt No:</strong> {{ $payment->receipt_no }}<br>
                        <strong>Date:</strong> {{ $payment->payment_date ? $payment->payment_date->format('d M Y') : '-' }}<br>
                        <strong>Status:</strong> {{ ucfirst($payment->status) }}
                    </div>

                    <br>
                    <span class="status">Payment Received</span>
                </td>
            </tr>
        </table>

        <div class="section-title">Student & Batch Details</div>

        <table class="info-table">
            <tr>
                <td>
                    <span class="label">Student Name</span>
                    <span class="value">{{ optional($payment->student)->name ?: '-' }}</span>
                </td>

                <td>
                    <span class="label">Student Code</span>
                    <span class="value">{{ optional($payment->student)->student_code ?: '-' }}</span>
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">Phone</span>
                    <span class="value">{{ optional($payment->student)->phone ?: '-' }}</span>
                </td>

                <td>
                    <span class="label">Batch</span>
                    <span class="value">{{ optional($payment->batch)->name ?: '-' }}</span>
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">Fee Plan</span>
                    <span class="value">{{ optional(optional($payment->assignment)->feePlan)->title ?: '-' }}</span>
                </td>

                <td>
                    <span class="label">Payment Mode</span>
                    <span class="value">{{ ucwords(str_replace('_', ' ', $payment->payment_mode)) }}</span>
                </td>
            </tr>
        </table>

        <div class="section-title">Payment Summary</div>

        <table class="payment-table">
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
                    <tr class="total-row">
                        <td>Balance After Payment</td>
                        <td>Rs. {{ number_format($payment->balance_after_payment, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        @if($payment->transaction_id || $payment->notes)
            <div class="note-box">
                @if($payment->transaction_id)
                    <strong>Transaction ID:</strong> {{ $payment->transaction_id }}<br>
                @endif

                @if($payment->notes)
                    <strong>Notes:</strong> {{ $payment->notes }}
                @endif
            </div>
        @endif

        @if($invoiceSetting->terms)
            <div class="terms">
                <strong>Terms:</strong> {{ $invoiceSetting->terms }}
            </div>
        @endif

        @if($invoiceSetting->show_signature ?? true)
            <table class="signature-table">
                <tr>
                    <td>
                        <div class="signature-line">Student / Parent Signature</div>
                    </td>

                    <td class="right-sign">
                        <div class="signature-line">
                            {{ $invoiceSetting->authorized_signature_label ?: 'Authorized Signature' }}
                        </div>
                    </td>
                </tr>
            </table>
        @endif

        @if($invoiceSetting->footer_note)
            <div class="footer-note">
                {{ $invoiceSetting->footer_note }}
            </div>
        @endif
    </div>
</body>
</html>