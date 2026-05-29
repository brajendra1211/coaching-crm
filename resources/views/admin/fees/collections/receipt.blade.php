@extends('admin.layouts.app')

@section('title', 'Fee Receipt')
@section('page_title', 'Fee Receipt')

@section('content')

@php
    $templateMap = [
        'modern' => 'global',
        'classic' => 'minimal',
        'compact' => 'compact-slip',
    ];

    $accent = $invoiceSetting->accent_color ?? '#2563eb';
    $invoiceTitle = $invoiceSetting->invoice_title ?? 'Fee Receipt';
    $defaultTemplate = $templateMap[$invoiceSetting->default_template] ?? ($invoiceSetting->default_template ?? 'global');

    if (!in_array($defaultTemplate, ['global', 'premium', 'letterhead', 'minimal', 'gst', 'compact-slip'], true)) {
        $defaultTemplate = 'global';
    }

    $instituteName = optional($setting)->institute_name ?? config('app.name');
    $logo = optional($setting)->logo;
    $address = optional($setting)->address;
    $phone = optional($setting)->phone;
    $email = optional($setting)->email;
@endphp

<style>
    .receipt-page { max-width: 1040px; margin: 0 auto; }
    .receipt-toolbar {
        display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;
        margin-bottom:12px;background:#fff;border:1px solid #e5e7eb;border-radius:16px;
        padding:10px 12px;box-shadow:0 8px 22px rgba(15,23,42,.05);
    }
    .toolbar-left,.toolbar-right{display:flex;gap:8px;flex-wrap:wrap;align-items:center;}
    .template-select{min-width:165px;border:1px solid #cbd5e1;border-radius:10px;padding:8px 10px;font-size:13px;font-weight:800;}
    .receipt-status-strip{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-bottom:12px;}
    .receipt-stat{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:11px 12px;box-shadow:0 8px 20px rgba(15,23,42,.05);}
    .receipt-stat small{display:block;color:#64748b;font-weight:900;text-transform:uppercase;font-size:10px;margin-bottom:5px;}
    .receipt-stat strong{color:#0f172a;font-size:16px;line-height:1.1;}

    .receipt-paper{max-width:760px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:18px;box-shadow:0 12px 34px rgba(15,23,42,.08);overflow:hidden;position:relative;}
    .receipt-head{padding:16px 18px;background:linear-gradient(135deg,{{ $accent }},#7c3aed);color:#fff;display:flex;justify-content:space-between;gap:16px;align-items:center;}
    .institute-box{display:flex;align-items:center;gap:11px;min-width:0;}
    .receipt-logo{width:52px;height:52px;border-radius:14px;object-fit:contain;background:#fff;padding:5px;flex-shrink:0;}
    .institute-box h2{margin:0 0 4px;color:#fff;font-size:18px;line-height:1.15;}
    .institute-box p{margin:0;color:rgba(255,255,255,.88);line-height:1.4;font-size:11px;}
    .receipt-title{text-align:right;min-width:165px;}
    .receipt-title h1{margin:0;color:#fff;font-size:22px;line-height:1.1;}
    .receipt-title .receipt-no{display:inline-flex;margin-top:7px;padding:6px 9px;border-radius:999px;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.22);color:#fff;font-weight:900;font-size:11px;}

    .receipt-body{padding:16px 18px;}
    .paid-badge{display:inline-flex;align-items:center;gap:6px;padding:7px 10px;border-radius:999px;background:#dcfce7;color:#166534;border:1px solid #bbf7d0;font-weight:900;font-size:11px;margin-bottom:12px;}
    .receipt-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:9px;margin-bottom:12px;}
    .receipt-box{background:#f8fafc;border:1px solid #e5e7eb;border-radius:12px;padding:9px 10px;}
    .receipt-box small{display:block;color:#64748b;font-weight:900;margin-bottom:4px;text-transform:uppercase;font-size:9.5px;}
    .receipt-box strong{color:#111827;font-size:12.5px;line-height:1.3;}

    .money-summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:9px;margin-bottom:12px;}
    .money-card{border-radius:14px;padding:12px;border:1px solid #e5e7eb;background:#fff;}
    .money-card.primary{background:linear-gradient(135deg,{{ $accent }},#7c3aed);color:#fff;border-color:transparent;}
    .money-card small{display:block;color:#64748b;font-weight:900;text-transform:uppercase;font-size:9.5px;margin-bottom:6px;}
    .money-card.primary small{color:rgba(255,255,255,.86);}
    .money-card strong{color:#111827;font-size:19px;line-height:1;}
    .money-card.primary strong{color:#fff;}

    .receipt-table{width:100%;border-collapse:separate;border-spacing:0;overflow:hidden;border:1px solid #e5e7eb;border-radius:13px;margin-top:10px;}
    .receipt-table th,.receipt-table td{padding:9px 10px;border-bottom:1px solid #e5e7eb;text-align:left;font-size:12px;}
    .receipt-table th{background:#f8fafc;color:#334155;font-size:10.5px;text-transform:uppercase;}
    .receipt-table tr:last-child td{border-bottom:0;}
    .receipt-table td:last-child,.receipt-table th:last-child{text-align:right;width:165px;}
    .received-row td{background:#eff6ff;color:#1d4ed8;font-size:13px;font-weight:900;}
    .amount-row td{font-weight:900;background:#f8fafc;}

    @media print {
        body *{visibility:hidden;}
        .receipt-paper,.receipt-paper *{visibility:visible;}
        .receipt-paper{position:absolute;left:0;top:0;width:100%;max-width:none;box-shadow:none;border-radius:0;border:0;}
        .receipt-toolbar,.receipt-status-strip,.sidebar,.topbar,.admin-header{display:none!important;}
    }

    @media(max-width:900px){
        .receipt-status-strip,.money-summary,.receipt-grid{grid-template-columns:repeat(2,minmax(0,1fr));}
        .receipt-head{align-items:flex-start;flex-direction:column;}
        .receipt-title{text-align:left;}
    }

    @media(max-width:620px){
        .receipt-toolbar,.toolbar-left,.toolbar-right{display:grid;width:100%;}
        .receipt-status-strip,.money-summary,.receipt-grid{grid-template-columns:1fr;}
    }
</style>

<div class="receipt-page">
    <div class="receipt-toolbar">
        <div class="toolbar-left">
            <a href="{{ route('admin.fee-collections.index') }}" class="btn btn-light">← Back</a>
            <a href="{{ route('admin.fee-collections.create') }}" class="btn btn-primary">+ Receive New Payment</a>
            <a href="{{ route('admin.invoice-settings.edit') }}" class="btn btn-light">Invoice Settings</a>
        </div>

        <div class="toolbar-right">
            <select class="template-select" id="templateSelect">
                <option value="global" {{ $defaultTemplate === 'global' ? 'selected' : '' }}>Global Standard</option>
                <option value="premium" {{ $defaultTemplate === 'premium' ? 'selected' : '' }}>Premium Receipt</option>
                <option value="letterhead" {{ $defaultTemplate === 'letterhead' ? 'selected' : '' }}>Letterhead</option>
                <option value="minimal" {{ $defaultTemplate === 'minimal' ? 'selected' : '' }}>Minimal Corporate</option>
                <option value="gst" {{ $defaultTemplate === 'gst' ? 'selected' : '' }}>GST Format</option>
                <option value="compact-slip" {{ $defaultTemplate === 'compact-slip' ? 'selected' : '' }}>Compact Slip</option>
            </select>

            <a href="{{ route('admin.fee-collections.receipt.pdf', ['feePayment' => $payment->id, 'template' => $defaultTemplate]) }}"
               class="btn btn-primary"
               id="downloadPdfBtn">
                Download PDF
            </a>

            <button type="button" class="btn btn-dark" onclick="window.print()">Print</button>
        </div>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:14px;margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="receipt-status-strip">
        <div class="receipt-stat">
            <small>Receipt No</small>
            <strong>{{ $payment->receipt_no }}</strong>
        </div>
        <div class="receipt-stat">
            <small>Paid Amount</small>
            <strong>₹{{ number_format($payment->amount, 2) }}</strong>
        </div>
        <div class="receipt-stat">
            <small>Payment Mode</small>
            <strong>{{ ucwords(str_replace('_', ' ', $payment->payment_mode)) }}</strong>
        </div>
        <div class="receipt-stat">
            <small>Balance</small>
            <strong>₹{{ number_format($payment->balance_after_payment, 2) }}</strong>
        </div>
    </div>

    <div class="receipt-paper">
        <div class="receipt-head">
            <div class="institute-box">
                @if(($invoiceSetting->show_logo ?? true) && !empty($logo))
                    <img src="{{ asset('storage/' . $logo) }}" class="receipt-logo" alt="{{ $instituteName }}">
                @endif

                <div>
                    <h2>{{ $instituteName }}</h2>
                    <p>
                        @if(($invoiceSetting->show_address ?? true) && !empty($address))
                            {{ $address }}
                        @endif

                        @if(($invoiceSetting->show_phone ?? true) && !empty($phone))
                            <br>Phone: {{ $phone }}
                        @endif

                        @if(($invoiceSetting->show_email ?? true) && !empty($email))
                            @if(!empty($phone)) | @endif Email: {{ $email }}
                        @endif
                    </p>
                </div>
            </div>

            <div class="receipt-title">
                <h1>{{ strtoupper($invoiceTitle) }}</h1>
                <span class="receipt-no">{{ $payment->receipt_no }}</span>
            </div>
        </div>

        <div class="receipt-body">
            <span class="paid-badge">✅ Payment Received Successfully</span>

            <div class="receipt-grid">
                <div class="receipt-box">
                    <small>Student Name</small>
                    <strong>{{ optional($payment->student)->name ?: '-' }}</strong>
                </div>
                <div class="receipt-box">
                    <small>Student Code</small>
                    <strong>{{ optional($payment->student)->student_code ?: '-' }}</strong>
                </div>
                <div class="receipt-box">
                    <small>Phone</small>
                    <strong>{{ optional($payment->student)->phone ?: '-' }}</strong>
                </div>
                <div class="receipt-box">
                    <small>Batch</small>
                    <strong>{{ optional($payment->batch)->name ?: '-' }}</strong>
                </div>
                <div class="receipt-box">
                    <small>Fee Plan</small>
                    <strong>{{ optional(optional($payment->assignment)->feePlan)->title ?: '-' }}</strong>
                </div>
                <div class="receipt-box">
                    <small>Payment Date</small>
                    <strong>{{ $payment->payment_date ? $payment->payment_date->format('d M Y') : '-' }}</strong>
                </div>
            </div>

            <div class="money-summary">
                <div class="money-card">
                    <small>Total Fee</small>
                    <strong>₹{{ number_format($payment->total_before_payment, 2) }}</strong>
                </div>
                <div class="money-card primary">
                    <small>Received</small>
                    <strong>₹{{ number_format($payment->amount, 2) }}</strong>
                </div>
                @if($invoiceSetting->show_balance ?? true)
                    <div class="money-card">
                        <small>Balance</small>
                        <strong>₹{{ number_format($payment->balance_after_payment, 2) }}</strong>
                    </div>
                @endif
            </div>

            <table class="receipt-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Fee</td>
                        <td>₹{{ number_format($payment->total_before_payment, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Balance Before Payment</td>
                        <td>₹{{ number_format($payment->balance_before_payment, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Fine Added</td>
                        <td>₹{{ number_format($payment->fine_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Discount Given</td>
                        <td>₹{{ number_format($payment->discount_amount, 2) }}</td>
                    </tr>
                    <tr class="received-row">
                        <td>Received Amount</td>
                        <td>₹{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    @if($invoiceSetting->show_balance ?? true)
                        <tr class="amount-row">
                            <td>Balance After Payment</td>
                            <td>₹{{ number_format($payment->balance_after_payment, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const templateSelect = document.getElementById('templateSelect');
        const downloadPdfBtn = document.getElementById('downloadPdfBtn');
        const baseUrl = @json(route('admin.fee-collections.receipt.pdf', ['feePayment' => $payment->id]));

        templateSelect?.addEventListener('change', function () {
            downloadPdfBtn.href = baseUrl + '?template=' + encodeURIComponent(this.value || 'global');
        });
    });
</script>

@endsection