<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $payment->receipt_no }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 12px;
            margin: 0;
        }

        .header {
            background: {{ $invoiceSetting->accent_color ?: '#2563eb' }};
            color: #fff;
            padding: 26px;
        }

        .row {
            width: 100%;
            display: table;
        }

        .col {
            display: table-cell;
            vertical-align: top;
        }

        .right {
            text-align: right;
        }

        .logo {
            width: 68px;
            height: 68px;
            object-fit: contain;
            background: #fff;
            border-radius: 10px;
            padding: 5px;
            margin-right: 12px;
        }

        .brand-wrap {
            display: table;
        }

        .brand-logo,
        .brand-info {
            display: table-cell;
            vertical-align: middle;
        }

        h1, h2, h3, p {
            margin: 0;
        }

        .body {
            padding: 26px;
        }

        .box {
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 12px;
        }

        .grid {
            width: 100%;
            display: table;
            margin-bottom: 14px;
        }

        .grid .cell {
            display: table-cell;
            width: 50%;
            padding-right: 10px;
            vertical-align: top;
        }

        small {
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            display: block;
            margin-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }

        th, td {
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #f1f5f9;
        }

        .total-row td {
            font-weight: bold;
            background: #eff6ff;
        }

        .footer {
            margin-top: 30px;
            color: #64748b;
            font-size: 11px;
        }

        .signature {
            margin-top: 48px;
            width: 220px;
            text-align: center;
            border-top: 1px solid #111827;
            padding-top: 8px;
            float: right;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="row">
        <div class="col">
            <div class="brand-wrap">
                @if($invoiceSetting->show_logo && !empty($setting->logo))
                    <div class="brand-logo">
                        <img src="{{ public_path('storage/' . $setting->logo) }}" class="logo">
                    </div>
                @endif

                <div class="brand-info">
                    <h2>{{ $setting->institute_name ?? config('app.name') }}</h2>
                    @if($invoiceSetting->show_address && !empty($setting->address))
                        <p>{{ $setting->address }}</p>
                    @endif
                    <p>
                        @if($invoiceSetting->show_phone && !empty($setting->phone))
                            Phone: {{ $setting->phone }}
                        @endif
                        @if($invoiceSetting->show_email && !empty($setting->email))
                            | Email: {{ $setting->email }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="col right">
            <h1>{{ $invoiceSetting->invoice_title ?: 'Fee Receipt' }}</h1>
            <p>{{ $payment->receipt_no }}</p>
            <p>{{ $payment->payment_date ? $payment->payment_date->format('d M Y') : '-' }}</p>
        </div>
    </div>
</div>

<div class="body">
    <div class="grid">
        <div class="cell">
            <div class="box">
                <small>Student</small>
                <strong>{{ optional($payment->student)->name ?: '-' }}</strong><br>
                Code: {{ optional($payment->student)->student_code ?: '-' }}<br>
                Phone: {{ optional($payment->student)->phone ?: '-' }}
            </div>
        </div>

        <div class="cell">
            <div class="box">
                <small>Batch / Plan</small>
                <strong>{{ optional($payment->batch)->name ?: '-' }}</strong><br>
                Plan: {{ optional(optional($payment->assignment)->feePlan)->title ?: '-' }}<br>
                Mode: {{ ucwords(str_replace('_', ' ', $payment->payment_mode)) }}
            </div>
        </div>
    </div>

    <table>
        <tr>
            <th>Description</th>
            <th>Amount</th>
        </tr>

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

        <tr class="total-row">
            <td>Received Amount</td>
            <td>Rs. {{ number_format($payment->amount, 2) }}</td>
        </tr>

        @if($invoiceSetting->show_balance)
            <tr class="total-row">
                <td>Balance After Payment</td>
                <td>Rs. {{ number_format($payment->balance_after_payment, 2) }}</td>
            </tr>
        @endif
    </table>

    @if($payment->transaction_id || $payment->notes)
        <div class="box" style="margin-top:16px;">
            @if($payment->transaction_id)
                <strong>Transaction ID:</strong> {{ $payment->transaction_id }}<br>
            @endif
            @if($payment->notes)
                <strong>Notes:</strong> {{ $payment->notes }}
            @endif
        </div>
    @endif

    @if($invoiceSetting->terms)
        <div class="footer">
            <strong>Terms:</strong> {{ $invoiceSetting->terms }}
        </div>
    @endif

    @if($invoiceSetting->show_signature)
        <div class="signature">
            {{ $invoiceSetting->authorized_signature_label ?: 'Authorized Signature' }}
        </div>
    @endif

    <div style="clear: both;"></div>

    @if($invoiceSetting->footer_note)
        <div class="footer" style="text-align:center;margin-top:45px;">
            {{ $invoiceSetting->footer_note }}
        </div>
    @endif
</div>

</body>
</html>