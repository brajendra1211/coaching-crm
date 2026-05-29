<style>
    .fee-form-wrap {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 360px;
        gap: 22px;
        align-items: start;
    }

    .form-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, .07);
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

    .form-group textarea {
        min-height: 115px;
        resize: vertical;
    }

    .fee-total-card {
        background: linear-gradient(135deg, #eff6ff, #fff);
        border: 1px solid #bfdbfe;
        border-radius: 20px;
        padding: 18px;
    }

    .fee-total-card small {
        display: block;
        color: #64748b;
        font-weight: 900;
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    .fee-total-card strong {
        color: #2563eb;
        font-size: 34px;
        line-height: 1;
    }

    .check-card {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        padding: 13px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        cursor: pointer;
    }

    .check-card input {
        width: auto;
        margin-top: 3px;
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
        .fee-form-wrap,
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

@php
    $status = old('status', $plan->status ?: 'active');
    $billingType = old('billing_type', $plan->billing_type ?: 'monthly');
@endphp

<div class="fee-form-wrap">
    <div>
        <div class="form-card">
            <div class="form-card-head">
                <h3>Plan Details</h3>
                <p>Map a fee plan with a batch.</p>
            </div>

            <div class="form-card-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Batch *</label>
                        <select name="batch_id" required>
                            <option value="">Select Batch</option>
                            @foreach($batches as $batch)
                                <option value="{{ $batch->id }}" {{ old('batch_id', $plan->batch_id) == $batch->id ? 'selected' : '' }}>
                                    {{ $batch->name }} {{ $batch->code ? '(' . $batch->code . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Plan Title *</label>
                        <input
                            type="text"
                            name="title"
                            value="{{ old('title', $plan->title) }}"
                            placeholder="NEET Morning Monthly Fee"
                            required
                        >
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Billing Type *</label>
                        <select name="billing_type" required>
                            <option value="monthly" {{ $billingType === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="one_time" {{ $billingType === 'one_time' ? 'selected' : '' }}>One Time</option>
                            <option value="installment" {{ $billingType === 'installment' ? 'selected' : '' }}>Installment</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" required>
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="form-card-head">
                <h3>Fee Components</h3>
                <p>Add all fee heads included in this plan.</p>
            </div>

            <div class="form-card-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Registration Fee</label>
                        <input type="number" step="0.01" min="0" name="registration_fee" class="fee-input" value="{{ old('registration_fee', $plan->registration_fee ?? 0) }}">
                    </div>

                    <div class="form-group">
                        <label>Admission Fee</label>
                        <input type="number" step="0.01" min="0" name="admission_fee" class="fee-input" value="{{ old('admission_fee', $plan->admission_fee ?? 0) }}">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Tuition Fee</label>
                        <input type="number" step="0.01" min="0" name="tuition_fee" class="fee-input" value="{{ old('tuition_fee', $plan->tuition_fee ?? 0) }}">
                    </div>

                    <div class="form-group">
                        <label>Exam Fee</label>
                        <input type="number" step="0.01" min="0" name="exam_fee" class="fee-input" value="{{ old('exam_fee', $plan->exam_fee ?? 0) }}">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Material Fee</label>
                        <input type="number" step="0.01" min="0" name="material_fee" class="fee-input" value="{{ old('material_fee', $plan->material_fee ?? 0) }}">
                    </div>

                    <div class="form-group">
                        <label>Other Fee</label>
                        <input type="number" step="0.01" min="0" name="other_fee" class="fee-input" value="{{ old('other_fee', $plan->other_fee ?? 0) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="form-card-head">
                <h3>Due Date & Fine</h3>
                <p>Monthly due day and late fine settings.</p>
            </div>

            <div class="form-card-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Due Day</label>
                        <input type="number" min="1" max="31" name="due_day" value="{{ old('due_day', $plan->due_day) }}" placeholder="10">
                    </div>

                    <div class="form-group">
                        <label>Fine Per Day</label>
                        <input type="number" step="0.01" min="0" name="fine_per_day" value="{{ old('fine_per_day', $plan->fine_per_day ?? 0) }}">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Effective From</label>
                        <input type="date" name="effective_from" value="{{ old('effective_from', $plan->effective_from ? $plan->effective_from->format('Y-m-d') : now()->format('Y-m-d')) }}">
                    </div>

                    <div class="form-group">
                        <label>Effective To</label>
                        <input type="date" name="effective_to" value="{{ old('effective_to', $plan->effective_to ? $plan->effective_to->format('Y-m-d') : '') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes">{{ old('notes', $plan->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
            <a href="{{ route('admin.batch-fee-plans.index') }}" class="btn btn-light">Cancel</a>
        </div>
    </div>

    <aside>
        <div class="form-card">
            <div class="form-card-head" style="background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;">
                <h3 style="color:#fff;">Fee Summary</h3>
                <p style="color:rgba(255,255,255,.9);">Live total and assignment options.</p>
            </div>

            <div class="form-card-body">
                <div class="fee-total-card">
                    <small>Total Fee</small>
                    <strong id="feeTotalPreview">₹0.00</strong>
                </div>

                <div style="margin-top:16px;">
                    <label class="check-card">
                        <input type="checkbox" name="apply_to_existing_students" value="1">
                        <div>
                            <strong>Apply to existing assigned students</strong>
                            <br>
                            <small style="color:#64748b;">
                                If checked, all students already assigned to this batch will get this fee plan.
                            </small>
                        </div>
                    </label>
                </div>

                <div style="margin-top:16px;background:#f8fafc;border:1px solid #e5e7eb;border-radius:16px;padding:14px;color:#64748b;line-height:1.7;font-size:13px;">
                    <strong style="color:#111827;">Note:</strong>
                    <br>
                    Only one active fee plan is allowed per batch. When this plan is active, old active plans for the same batch will become inactive.
                </div>
            </div>
        </div>
    </aside>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const feeInputs = document.querySelectorAll('.fee-input');
        const preview = document.getElementById('feeTotalPreview');

        function updateTotal() {
            let total = 0;

            feeInputs.forEach(function (input) {
                total += parseFloat(input.value || 0);
            });

            preview.textContent = '₹' + total.toFixed(2);
        }

        feeInputs.forEach(function (input) {
            input.addEventListener('input', updateTotal);
        });

        updateTotal();
    });
</script>