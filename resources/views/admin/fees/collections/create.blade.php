@extends('admin.layouts.app')

@section('title', 'Receive Fee Payment')
@section('page_title', 'Receive Fee Payment')

@section('content')

<style>
    .collection-wrap {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 360px;
        gap: 22px;
        align-items: start;
    }

    .form-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 12px 35px rgba(15,23,42,.07);
        margin-bottom: 22px;
        overflow: hidden;
    }

    .form-card-head {
        padding: 18px 22px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #fff, #f8fafc);
    }

    .form-card-head h3 {
        margin: 0;
        color: #111827;
        font-size: 19px;
    }

    .form-card-head p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .form-card-body {
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

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 14px;
        padding: 13px 14px;
        font-size: 15px;
        outline: none;
        background: #fff;
        color: #111827;
        font-family: inherit;
    }

    .summary-box {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 14px;
        margin-bottom: 12px;
    }

    .summary-box small {
        display: block;
        color: #64748b;
        font-weight: 900;
        margin-bottom: 6px;
        text-transform: uppercase;
        font-size: 12px;
    }

    .summary-box strong {
        color: #111827;
        font-size: 18px;
    }

    .balance-card {
        background: linear-gradient(135deg, #eff6ff, #fff);
        border: 1px solid #bfdbfe;
        border-radius: 20px;
        padding: 18px;
    }

    .balance-card small {
        display: block;
        color: #64748b;
        font-weight: 900;
        margin-bottom: 8px;
    }

    .balance-card strong {
        color: #2563eb;
        font-size: 34px;
        line-height: 1;
    }

    .error-box {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: 14px;
        border-radius: 14px;
        margin-bottom: 18px;
    }

    @media(max-width: 1050px) {
        .collection-wrap,
        .grid-2 {
            grid-template-columns: 1fr;
        }
    }
</style>

@if($errors->any())
    <div class="error-box">
        <strong>Please fix errors:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.fee-collections.store') }}">
    @csrf

    <div class="collection-wrap">
        <div>
            <div class="form-card">
                <div class="form-card-head">
                    <h3>Student Fee Assignment</h3>
                    <p>Select the student fee assignment for payment collection.</p>
                </div>

                <div class="form-card-body">
                    <div class="form-group">
                        <label>Student Fee Assignment *</label>

                        <select name="student_fee_assignment_id" id="assignmentSelect" required>
                            <option value="">Select Student / Batch / Fee Plan</option>

                            @foreach($assignments as $assignment)
                                <option
                                    value="{{ $assignment->id }}"
                                    data-student="{{ optional($assignment->student)->name }}"
                                    data-code="{{ optional($assignment->student)->student_code }}"
                                    data-batch="{{ optional($assignment->batch)->name }}"
                                    data-plan="{{ optional($assignment->feePlan)->title }}"
                                    data-total="{{ $assignment->total_amount }}"
                                    data-paid="{{ $assignment->paid_amount }}"
                                    data-discount="{{ $assignment->discount_amount }}"
                                    data-fine="{{ $assignment->fine_amount }}"
                                    data-balance="{{ $assignment->balance_amount }}"
                                    {{ old('student_fee_assignment_id', optional($selectedAssignment)->id) == $assignment->id ? 'selected' : '' }}
                                >
                                    {{ optional($assignment->student)->name }}
                                    |
                                    {{ optional($assignment->student)->student_code }}
                                    |
                                    {{ optional($assignment->batch)->name }}
                                    |
                                    Balance ₹{{ number_format($assignment->balance_amount, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="form-card-head">
                    <h3>Payment Details</h3>
                    <p>Enter received amount, discount, fine and payment mode.</p>
                </div>

                <div class="form-card-body">
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Payment Date *</label>
                            <input type="date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Payment Mode *</label>
                            @php $mode = old('payment_mode', 'cash'); @endphp

                            <select name="payment_mode" required>
                                <option value="cash" {{ $mode === 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="upi" {{ $mode === 'upi' ? 'selected' : '' }}>UPI</option>
                                <option value="bank_transfer" {{ $mode === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="card" {{ $mode === 'card' ? 'selected' : '' }}>Card</option>
                                <option value="cheque" {{ $mode === 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="other" {{ $mode === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Received Amount *</label>
                            <input type="number" step="0.01" min="0.01" name="amount" id="amountInput" value="{{ old('amount') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Discount Amount</label>
                            <input type="number" step="0.01" min="0" name="discount_amount" id="discountInput" value="{{ old('discount_amount', 0) }}">
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Fine Amount</label>
                            <input type="number" step="0.01" min="0" name="fine_amount" id="fineInput" value="{{ old('fine_amount', 0) }}">
                        </div>

                        <div class="form-group">
                            <label>Transaction ID</label>
                            <input type="text" name="transaction_id" value="{{ old('transaction_id') }}" placeholder="UPI / Bank / Cheque reference">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" placeholder="Optional payment note">{{ old('notes') }}</textarea>
                    </div>

                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        <button type="submit" class="btn btn-primary">Receive Payment</button>
                        <a href="{{ route('admin.fee-collections.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>
            </div>
        </div>

        <aside>
            <div class="form-card">
                <div class="form-card-head" style="background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;">
                    <h3 style="color:#fff;">Fee Summary</h3>
                    <p style="color:rgba(255,255,255,.9);">Live student fee overview.</p>
                </div>

                <div class="form-card-body">
                    <div class="summary-box">
                        <small>Student</small>
                        <strong id="studentNamePreview">-</strong>
                    </div>

                    <div class="summary-box">
                        <small>Batch</small>
                        <strong id="batchNamePreview">-</strong>
                    </div>

                    <div class="summary-box">
                        <small>Fee Plan</small>
                        <strong id="planNamePreview">-</strong>
                    </div>

                    <div class="summary-box">
                        <small>Total Fee</small>
                        <strong id="totalPreview">₹0.00</strong>
                    </div>

                    <div class="summary-box">
                        <small>Already Paid</small>
                        <strong id="paidPreview">₹0.00</strong>
                    </div>

                    <div class="summary-box">
                        <small>Current Balance</small>
                        <strong id="currentBalancePreview">₹0.00</strong>
                    </div>

                    <div class="balance-card">
                        <small>Balance After Payment</small>
                        <strong id="afterBalancePreview">₹0.00</strong>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const assignmentSelect = document.getElementById('assignmentSelect');
        const amountInput = document.getElementById('amountInput');
        const discountInput = document.getElementById('discountInput');
        const fineInput = document.getElementById('fineInput');

        const studentNamePreview = document.getElementById('studentNamePreview');
        const batchNamePreview = document.getElementById('batchNamePreview');
        const planNamePreview = document.getElementById('planNamePreview');
        const totalPreview = document.getElementById('totalPreview');
        const paidPreview = document.getElementById('paidPreview');
        const currentBalancePreview = document.getElementById('currentBalancePreview');
        const afterBalancePreview = document.getElementById('afterBalancePreview');

        let currentBalance = 0;

        function money(value) {
            return '₹' + Number(value || 0).toFixed(2);
        }

        function updateAssignmentPreview() {
            const selected = assignmentSelect.options[assignmentSelect.selectedIndex];

            if (!selected || !selected.value) {
                currentBalance = 0;
                studentNamePreview.textContent = '-';
                batchNamePreview.textContent = '-';
                planNamePreview.textContent = '-';
                totalPreview.textContent = money(0);
                paidPreview.textContent = money(0);
                currentBalancePreview.textContent = money(0);
                updateAfterBalance();
                return;
            }

            currentBalance = Number(selected.dataset.balance || 0);

            studentNamePreview.textContent = (selected.dataset.student || '-') + ' (' + (selected.dataset.code || '-') + ')';
            batchNamePreview.textContent = selected.dataset.batch || '-';
            planNamePreview.textContent = selected.dataset.plan || '-';
            totalPreview.textContent = money(selected.dataset.total);
            paidPreview.textContent = money(selected.dataset.paid);
            currentBalancePreview.textContent = money(currentBalance);

            if (!amountInput.value) {
                amountInput.value = currentBalance > 0 ? currentBalance.toFixed(2) : '';
            }

            updateAfterBalance();
        }

        function updateAfterBalance() {
            const amount = Number(amountInput.value || 0);
            const discount = Number(discountInput.value || 0);
            const fine = Number(fineInput.value || 0);

            const after = Math.max(0, (currentBalance + fine) - (amount + discount));

            afterBalancePreview.textContent = money(after);
        }

        assignmentSelect.addEventListener('change', function () {
            amountInput.value = '';
            updateAssignmentPreview();
        });

        [amountInput, discountInput, fineInput].forEach(function (input) {
            input.addEventListener('input', updateAfterBalance);
        });

        updateAssignmentPreview();
    });
</script>

@endsection