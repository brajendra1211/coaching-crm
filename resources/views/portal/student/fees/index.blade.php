@extends('portal.layouts.app', ['portalRole' => 'student'])

@section('title', 'Fee Details')
@section('page_title', 'Fees')
@section('page_subtitle', 'Fee plan, due amount and payment receipts')

@section('content')
<div class="grid-4">
    <div class="card stat"><small>Total Payable</small><strong>Rs. {{ number_format((float) $totalPayable, 2) }}</strong></div>
    <div class="card stat"><small>Total Paid</small><strong>Rs. {{ number_format((float) $totalPaid, 2) }}</strong></div>
    <div class="card stat"><small>Pending</small><strong>Rs. {{ number_format((float) $totalPending, 2) }}</strong></div>
    <div class="card stat"><small>Fee Plans</small><strong>{{ $feeAssignments->count() }}</strong></div>
</div>

<section class="card" style="margin-bottom:18px;">
    <h3>Assigned Fee Plans</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Batch</th><th>Billing</th><th>Total</th><th>Paid</th><th>Discount</th><th>Fine</th><th>Balance</th><th>Next Due</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($feeAssignments as $fee)
                    <tr>
                        <td>{{ $fee->batch->name ?? '-' }}</td>
                        <td>{{ ucfirst($fee->billing_type ?? '-') }}</td>
                        <td>Rs. {{ number_format((float) $fee->total_amount, 2) }}</td>
                        <td>Rs. {{ number_format((float) $fee->paid_amount, 2) }}</td>
                        <td>Rs. {{ number_format((float) $fee->discount_amount, 2) }}</td>
                        <td>Rs. {{ number_format((float) $fee->fine_amount, 2) }}</td>
                        <td>Rs. {{ number_format((float) $fee->balance_amount, 2) }}</td>
                        <td>{{ $fee->next_due_date ? $fee->next_due_date->format('d M Y') : '-' }}</td>
                        <td><span class="pill {{ (float) $fee->balance_amount > 0 ? 'orange' : 'green' }}">{{ ucfirst($fee->status ?? 'active') }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="9" style="text-align:center;color:#64748b;">No fee plan assigned.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="card">
    <h3>Payment History</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Receipt</th><th>Date</th><th>Batch</th><th>Amount</th><th>Mode</th><th>Transaction</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->receipt_no }}</td>
                        <td>{{ $payment->payment_date ? $payment->payment_date->format('d M Y') : '-' }}</td>
                        <td>{{ $payment->batch->name ?? '-' }}</td>
                        <td>Rs. {{ number_format((float) $payment->amount, 2) }}</td>
                        <td>{{ ucfirst($payment->payment_mode ?? '-') }}</td>
                        <td>{{ $payment->transaction_id ?: '-' }}</td>
                        <td><span class="pill green">{{ ucfirst($payment->status ?? 'paid') }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;color:#64748b;">No payments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $payments->links() }}
</section>
@endsection
