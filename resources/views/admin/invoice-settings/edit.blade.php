@extends('admin.layouts.app')

@section('title', 'Invoice Settings')
@section('page_title', 'Invoice Settings')

@section('content')

<style>
    .invoice-settings-wrap {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 380px;
        gap: 22px;
        align-items: start;
    }

    .settings-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 12px 35px rgba(15,23,42,.07);
        margin-bottom: 22px;
        overflow: hidden;
    }

    .settings-head {
        padding: 18px 22px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #fff, #f8fafc);
    }

    .settings-head h3 {
        margin: 0;
        color: #111827;
        font-size: 20px;
    }

    .settings-head p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .settings-body {
        padding: 22px;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        font-weight: 900;
        color: #334155;
        margin-bottom: 8px;
    }

    .switch-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .switch-card {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        padding: 13px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        cursor: pointer;
    }

    .switch-card input {
        width: auto;
        margin-top: 3px;
    }

    .preview-card {
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        overflow: hidden;
        background: #fff;
    }

    .preview-top {
        padding: 18px;
        color: #fff;
        background: {{ $invoiceSetting->accent_color ?: '#2563eb' }};
    }

    .preview-body {
        padding: 18px;
    }

    .preview-line {
        height: 12px;
        background: #e5e7eb;
        border-radius: 999px;
        margin-bottom: 10px;
    }

    @media(max-width: 1000px) {
        .invoice-settings-wrap,
        .grid-2,
        .switch-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@if(session('success'))
    <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;padding:12px 14px;border-radius:12px;margin-bottom:15px;">
        <strong>Please fix errors:</strong>
        <ul style="margin:8px 0 0;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.invoice-settings.update') }}">
    @csrf
    @method('PUT')

    <div class="invoice-settings-wrap">
        <main>
            <div class="settings-card">
                <div class="settings-head">
                    <h3>Invoice Format</h3>
                    <p>Choose default invoice style, paper size and primary color.</p>
                </div>

                <div class="settings-body">
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Invoice Title</label>
                            <input type="text" name="invoice_title" value="{{ old('invoice_title', $invoiceSetting->invoice_title) }}">
                        </div>

                        <div class="form-group">
                            <label>Invoice Prefix</label>
                            <input type="text" name="invoice_prefix" value="{{ old('invoice_prefix', $invoiceSetting->invoice_prefix) }}">
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Default Template</label>
                            <select name="default_template">
                                <option value="global" {{ old('default_template', $invoiceSetting->default_template) === 'global' ? 'selected' : '' }}>Global Standard</option>
                                <option value="premium" {{ old('default_template', $invoiceSetting->default_template) === 'premium' ? 'selected' : '' }}>Premium Receipt</option>
                                <option value="letterhead" {{ old('default_template', $invoiceSetting->default_template) === 'letterhead' ? 'selected' : '' }}>Letterhead Format</option>
                                <option value="minimal" {{ old('default_template', $invoiceSetting->default_template) === 'minimal' ? 'selected' : '' }}>Minimal Corporate</option>
                                <option value="gst" {{ old('default_template', $invoiceSetting->default_template) === 'gst' ? 'selected' : '' }}>GST Format</option>
                                <option value="compact-slip" {{ old('default_template', $invoiceSetting->default_template) === 'compact-slip' ? 'selected' : '' }}>Compact Slip</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Paper Size</label>
                            <select name="paper_size">
                                <option value="A4" {{ old('paper_size', $invoiceSetting->paper_size) === 'A4' ? 'selected' : '' }}>A4</option>
                                <option value="A5" {{ old('paper_size', $invoiceSetting->paper_size) === 'A5' ? 'selected' : '' }}>A5</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Accent Color</label>
                        <input type="color" name="accent_color" value="{{ old('accent_color', $invoiceSetting->accent_color ?: '#2563eb') }}">
                    </div>
                </div>
            </div>

            <div class="settings-card">
                <div class="settings-head">
                    <h3>Visible Fields</h3>
                    <p>Control what should appear on invoice PDF.</p>
                </div>

                <div class="settings-body">
                    <div class="switch-grid">
                        @foreach([
                            'show_logo' => 'Show Logo',
                            'show_address' => 'Show Address',
                            'show_phone' => 'Show Phone',
                            'show_email' => 'Show Email',
                            'show_signature' => 'Show Signature',
                            'show_balance' => 'Show Balance',
                        ] as $field => $label)
                            <label class="switch-card">
                                <input type="checkbox" name="{{ $field }}" value="1" {{ old($field, $invoiceSetting->$field) ? 'checked' : '' }}>
                                <span>
                                    <strong>{{ $label }}</strong>
                                    <br>
                                    <small style="color:#64748b;">Enable this field on PDF invoice.</small>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="settings-card">
                <div class="settings-head">
                    <h3>Terms & Footer</h3>
                    <p>Add receipt terms, notes and signature label.</p>
                </div>

                <div class="settings-body">
                    <div class="form-group">
                        <label>Signature Label</label>
                        <input type="text" name="authorized_signature_label" value="{{ old('authorized_signature_label', $invoiceSetting->authorized_signature_label) }}">
                    </div>

                    <div class="form-group">
                        <label>Terms</label>
                        <textarea name="terms">{{ old('terms', $invoiceSetting->terms) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Footer Note</label>
                        <textarea name="footer_note">{{ old('footer_note', $invoiceSetting->footer_note) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Invoice Settings</button>
                </div>
            </div>
        </main>

        <aside>
            <div class="settings-card">
                <div class="settings-head" style="background:linear-gradient(135deg,#2563eb,#7c3aed);">
                    <h3 style="color:#fff;">Live Preview</h3>
                    <p style="color:rgba(255,255,255,.86);">Basic invoice style preview.</p>
                </div>

                <div class="settings-body">
                    <div class="preview-card">
                        <div class="preview-top">
                            <strong>{{ $invoiceSetting->invoice_title ?: 'Fee Receipt' }}</strong>
                            <br>
                            <small>{{ strtoupper($invoiceSetting->default_template ?: 'modern') }} TEMPLATE</small>
                        </div>

                        <div class="preview-body">
                            <div class="preview-line" style="width:90%;"></div>
                            <div class="preview-line" style="width:70%;"></div>
                            <div class="preview-line" style="width:95%;"></div>
                            <div class="preview-line" style="width:60%;"></div>

                            <div style="margin-top:18px;padding:14px;border-radius:16px;background:#eff6ff;color:#1d4ed8;font-weight:900;">
                                Receipt PDF will use selected template.
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:16px;color:#64748b;line-height:1.7;font-size:13px;">
                        After saving settings, all new receipt PDFs will use the selected format by default.
                    </div>
                </div>
            </div>
        </aside>
    </div>
</form>

@endsection