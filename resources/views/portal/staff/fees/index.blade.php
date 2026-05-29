@extends('portal.layouts.app', ['portalRole' => 'staff'])
@section('title', 'Fees')
@section('page_title', 'Fees')
@section('page_subtitle', 'Fee assignments, pending balance and recent collections')
@section('content')
<div class="grid-2"><div class="card stat"><small>Total Paid</small><strong>Rs. {{ number_format((float) $totalPaid, 2) }}</strong></div><div class="card stat"><small>Total Pending</small><strong>Rs. {{ number_format((float) $totalPending, 2) }}</strong></div></div>
<div class="page-actions"><a href="{{ route('admin.fee-collections.create') }}" class="btn btn-primary">Collect Fee</a><a href="{{ route('admin.fees.index') }}" class="btn btn-light">Fee Dashboard</a></div>
<section class="card" style="margin-bottom:18px;"><h3>Fee Assignments</h3><div class="table-wrap"><table>
<thead><tr><th>Student</th><th>Batch</th><th>Total</th><th>Paid</th><th>Balance</th><th>Next Due</th><th>Status</th></tr></thead><tbody>
@forelse($assignments as $fee)
<tr><td>{{ $fee->student->name ?? '-' }}</td><td>{{ $fee->batch->name ?? '-' }}</td><td>Rs. {{ number_format((float) $fee->total_amount, 2) }}</td><td>Rs. {{ number_format((float) $fee->paid_amount, 2) }}</td><td>Rs. {{ number_format((float) $fee->balance_amount, 2) }}</td><td>{{ $fee->next_due_date ? $fee->next_due_date->format('d M Y') : '-' }}</td><td><span class="pill {{ (float) $fee->balance_amount > 0 ? 'orange' : 'green' }}">{{ ucfirst($fee->status) }}</span></td></tr>
@empty <tr><td colspan="7" style="text-align:center;color:#64748b;">No fee assignments found.</td></tr> @endforelse
</tbody></table></div>{{ $assignments->links() }}</section>
<section class="card"><h3>Recent Payments</h3><div class="table-wrap"><table>
<thead><tr><th>Receipt</th><th>Student</th><th>Batch</th><th>Amount</th><th>Date</th><th>Mode</th></tr></thead><tbody>
@forelse($payments as $payment)
<tr><td>{{ $payment->receipt_no }}</td><td>{{ $payment->student->name ?? '-' }}</td><td>{{ $payment->batch->name ?? '-' }}</td><td>Rs. {{ number_format((float) $payment->amount, 2) }}</td><td>{{ $payment->payment_date ? $payment->payment_date->format('d M Y') : '-' }}</td><td>{{ ucfirst($payment->payment_mode ?? '-') }}</td></tr>
@empty <tr><td colspan="6" style="text-align:center;color:#64748b;">No payments found.</td></tr> @endforelse
</tbody></table></div></section>
@endsection
