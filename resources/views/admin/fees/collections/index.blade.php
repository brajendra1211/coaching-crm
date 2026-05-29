@extends('admin.layouts.app')

@section('title', 'Fee Collections')
@section('page_title', 'Fee Collections')

@section('content')

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 18px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 18px;
        box-shadow: 0 12px 35px rgba(15,23,42,.07);
    }

    .stat-card small {
        display: block;
        color: #64748b;
        font-weight: 900;
        margin-bottom: 8px;
        text-transform: uppercase;
        font-size: 12px;
    }

    .stat-card strong {
        color: #111827;
        font-size: 28px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 200px 170px 160px 160px auto;
        gap: 12px;
        margin-bottom: 18px;
    }

    .status-pill {
        display: inline-flex;
        padding: 7px 11px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        border: 1px solid;
        white-space: nowrap;
    }

    .status-paid {
        background: #dcfce7;
        color: #166534;
        border-color: #bbf7d0;
    }

    .status-void {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .action-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    @media(max-width: 1100px) {
        .filter-grid,
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="stats-grid">
    <div class="stat-card">
        <small>Today Collection</small>
        <strong>₹{{ number_format($todayCollection, 2) }}</strong>
    </div>

    <div class="stat-card">
        <small>Total Collection</small>
        <strong>₹{{ number_format($totalCollection, 2) }}</strong>
    </div>

    <div class="stat-card">
        <small>Total Pending</small>
        <strong>₹{{ number_format($pendingAmount, 2) }}</strong>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Fee Collections</h3>
            <p style="margin:6px 0 0;color:#64748b;">
                Manage student fee payments, receipts and payment history.
            </p>
        </div>

        <a href="{{ route('admin.fee-collections.create') }}" class="btn btn-primary">
            + Receive Payment
        </a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:12px 14px;border-radius:12px;margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" class="filter-grid">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search receipt, student, batch...">

        <select name="batch_id">
            <option value="">All Batches</option>
            @foreach($batches as $batch)
                <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                    {{ $batch->name }}
                </option>
            @endforeach
        </select>

        <select name="payment_mode">
            <option value="">All Modes</option>
            <option value="cash" {{ request('payment_mode') === 'cash' ? 'selected' : '' }}>Cash</option>
            <option value="upi" {{ request('payment_mode') === 'upi' ? 'selected' : '' }}>UPI</option>
            <option value="bank_transfer" {{ request('payment_mode') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
            <option value="card" {{ request('payment_mode') === 'card' ? 'selected' : '' }}>Card</option>
            <option value="cheque" {{ request('payment_mode') === 'cheque' ? 'selected' : '' }}>Cheque</option>
            <option value="other" {{ request('payment_mode') === 'other' ? 'selected' : '' }}>Other</option>
        </select>

        <input type="date" name="date_from" value="{{ request('date_from') }}">
        <input type="date" name="date_to" value="{{ request('date_to') }}">

        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Receipt</th>
                    <th>Student</th>
                    <th>Batch</th>
                    <th>Amount</th>
                    <th>Mode</th>
                    <th>Status</th>
                    <th width="260">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>
                            <strong>{{ $payment->receipt_no }}</strong>
                            <br>
                            <small style="color:#64748b;">
                                {{ $payment->payment_date ? $payment->payment_date->format('d M Y') : '-' }}
                            </small>
                        </td>

                        <td>
                            {{ optional($payment->student)->name ?: '-' }}
                            <br>
                            <small style="color:#64748b;">
                                {{ optional($payment->student)->student_code ?: '-' }}
                            </small>
                        </td>

                        <td>
                            {{ optional($payment->batch)->name ?: '-' }}
                            <br>
                            <small style="color:#64748b;">
                                {{ optional($payment->batch)->code ?: '-' }}
                            </small>
                        </td>

                        <td>
                            <strong>₹{{ number_format($payment->amount, 2) }}</strong>
                            <br>
                            <small style="color:#64748b;">
                                Balance: ₹{{ number_format($payment->balance_after_payment, 2) }}
                            </small>
                        </td>

                        <td>
                            {{ ucwords(str_replace('_', ' ', $payment->payment_mode)) }}
                            @if($payment->transaction_id)
                                <br>
                                <small style="color:#64748b;">{{ $payment->transaction_id }}</small>
                            @endif
                        </td>

                        <td>
                            <span class="status-pill status-{{ $payment->status }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>

                        <td>
                            <div class="action-row">
                                <a href="{{ route('admin.fee-collections.receipt', $payment) }}" class="btn btn-light">
                                    Receipt
                                </a>

                                @if($payment->status === 'paid')
                                    <form method="POST" action="{{ route('admin.fee-collections.destroy', $payment) }}" onsubmit="return confirm('Void this payment?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-danger">Void</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:35px;color:#64748b;">
                            No fee collections found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:22px;">
        {{ $payments->links() }}
    </div>
</div>

@endsection