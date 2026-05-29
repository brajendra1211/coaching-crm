@extends('admin.layouts.app')

@section('title', 'Fees Dashboard')
@section('page_title', 'Fees Dashboard')

@section('content')

<style>
    .fees-hero {
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.22), transparent 30%),
            linear-gradient(135deg, #2563eb, #7c3aed);
        border-radius: 26px;
        padding: 26px;
        color: #fff;
        box-shadow: 0 18px 45px rgba(37,99,235,.22);
        margin-bottom: 22px;
        display: flex;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        align-items: center;
    }

    .fees-hero h2 {
        margin: 0 0 8px;
        color: #fff;
        font-size: 28px;
        letter-spacing: -.5px;
    }

    .fees-hero p {
        margin: 0;
        color: rgba(255,255,255,.88);
        line-height: 1.6;
    }

    .hero-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .hero-actions .btn {
        background: #fff;
        color: #1d4ed8;
        border-color: rgba(255,255,255,.45);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        padding: 18px;
        box-shadow: 0 12px 35px rgba(15,23,42,.07);
        position: relative;
        overflow: hidden;
    }

    .stat-card::after {
        content: "";
        position: absolute;
        right: -30px;
        top: -30px;
        width: 90px;
        height: 90px;
        border-radius: 999px;
        background: rgba(37,99,235,.08);
    }

    .stat-card small {
        display: block;
        color: #64748b;
        font-weight: 900;
        margin-bottom: 8px;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: .4px;
    }

    .stat-card strong {
        color: #0f172a;
        font-size: 28px;
        line-height: 1;
    }

    .stat-card span {
        display: block;
        margin-top: 8px;
        color: #64748b;
        font-size: 13px;
        font-weight: 700;
    }

    .fees-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(360px, .8fr);
        gap: 22px;
        align-items: start;
    }

    .mini-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 12px 35px rgba(15,23,42,.07);
        margin-bottom: 22px;
        overflow: hidden;
    }

    .mini-card-head {
        padding: 18px 22px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #fff, #f8fafc);
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    .mini-card-head h3 {
        margin: 0;
        color: #111827;
        font-size: 20px;
    }

    .mini-card-head p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .mini-card-body {
        padding: 20px 22px;
    }

    .quick-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .quick-link {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 16px;
        background: #f8fafc;
        transition: .2s ease;
    }

    .quick-link:hover {
        transform: translateY(-2px);
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .quick-link strong {
        display: block;
        color: #111827;
        margin-bottom: 6px;
    }

    .quick-link small {
        color: #64748b;
        line-height: 1.5;
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

    .status-active {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    .amount-due {
        color: #dc2626;
        font-weight: 900;
    }

    .amount-paid {
        color: #16a34a;
        font-weight: 900;
    }

    @media(max-width: 1150px) {
        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .fees-layout {
            grid-template-columns: 1fr;
        }
    }

    @media(max-width: 700px) {
        .stats-grid,
        .quick-grid {
            grid-template-columns: 1fr;
        }

        .fees-hero {
            padding: 22px;
        }

        .fees-hero h2 {
            font-size: 23px;
        }
    }
</style>

<div class="fees-hero">
    <div>
        <h2>Fees & Finance Control Center</h2>
        <p>
            Track fee plans, student dues, collections, receipts and batch-wise pending amount from one place.
        </p>
    </div>

    <div class="hero-actions">
        <a href="{{ route('admin.batch-fee-plans.create') }}" class="btn">+ Create Fee Plan</a>
        <a href="{{ route('admin.fee-collections.create') }}" class="btn">+ Receive Payment</a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <small>Today Collection</small>
        <strong>₹{{ number_format($todayCollection, 2) }}</strong>
        <span>Payments received today</span>
    </div>

    <div class="stat-card">
        <small>This Month</small>
        <strong>₹{{ number_format($monthCollection, 2) }}</strong>
        <span>Current month collection</span>
    </div>

    <div class="stat-card">
        <small>Total Collection</small>
        <strong>₹{{ number_format($totalCollection, 2) }}</strong>
        <span>All paid receipts</span>
    </div>

    <div class="stat-card">
        <small>Total Pending</small>
        <strong>₹{{ number_format($totalPending, 2) }}</strong>
        <span>{{ $pendingStudents }} students with dues</span>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <small>Active Fee Plans</small>
        <strong>{{ $activeFeePlans }}</strong>
        <span>Batch fee plans currently active</span>
    </div>

    <div class="stat-card">
        <small>Fee Assignments</small>
        <strong>{{ $activeAssignments }}</strong>
        <span>Active student fee assignments</span>
    </div>

    <div class="stat-card">
        <small>Pending Students</small>
        <strong>{{ $pendingStudents }}</strong>
        <span>Students with balance amount</span>
    </div>

    <div class="stat-card">
        <small>Collection Ratio</small>
        @php
            $totalExpected = $totalCollection + $totalPending;
            $ratio = $totalExpected > 0 ? round(($totalCollection / $totalExpected) * 100) : 0;
        @endphp
        <strong>{{ $ratio }}%</strong>
        <span>Collected vs total expected</span>
    </div>
</div>

<div class="fees-layout">
    <main>
        <div class="mini-card">
            <div class="mini-card-head">
                <div>
                    <h3>Batch-wise Fee Summary</h3>
                    <p>Collection and pending status by batch.</p>
                </div>

                <a href="{{ route('admin.batch-fee-plans.index') }}" class="btn btn-light">View Plans</a>
            </div>

            <div class="mini-card-body">
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Batch</th>
                                <th>Students</th>
                                <th>Total Fee</th>
                                <th>Paid</th>
                                <th>Pending</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($batchFeeStats as $row)
                                <tr>
                                    <td>
                                        <strong>{{ optional($row->batch)->name ?: '-' }}</strong>
                                        <br>
                                        <small style="color:#64748b;">{{ optional($row->batch)->code ?: '-' }}</small>
                                    </td>

                                    <td>{{ $row->total_students }}</td>

                                    <td>₹{{ number_format($row->total_fee ?? 0, 2) }}</td>

                                    <td class="amount-paid">
                                        ₹{{ number_format($row->total_paid ?? 0, 2) }}
                                    </td>

                                    <td class="amount-due">
                                        ₹{{ number_format($row->total_pending ?? 0, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center;padding:30px;color:#64748b;">
                                        No batch fee data found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mini-card">
            <div class="mini-card-head">
                <div>
                    <h3>Highest Pending Fees</h3>
                    <p>Students with the highest outstanding balance.</p>
                </div>

                <a href="{{ route('admin.fee-collections.create') }}" class="btn btn-primary">Collect Fee</a>
            </div>

            <div class="mini-card-body">
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Batch</th>
                                <th>Fee Plan</th>
                                <th>Balance</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($pendingFees as $assignment)
                                <tr>
                                    <td>
                                        <strong>{{ optional($assignment->student)->name ?: '-' }}</strong>
                                        <br>
                                        <small style="color:#64748b;">{{ optional($assignment->student)->student_code ?: '-' }}</small>
                                    </td>

                                    <td>{{ optional($assignment->batch)->name ?: '-' }}</td>

                                    <td>{{ optional($assignment->feePlan)->title ?: '-' }}</td>

                                    <td class="amount-due">
                                        ₹{{ number_format($assignment->balance_amount, 2) }}
                                    </td>

                                    <td>
                                        <a href="{{ route('admin.fee-collections.create', ['assignment_id' => $assignment->id]) }}" class="btn btn-light">
                                            Receive
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align:center;padding:30px;color:#64748b;">
                                        No pending fees found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <aside>
        <div class="mini-card">
            <div class="mini-card-head">
                <div>
                    <h3>Quick Actions</h3>
                    <p>Common fee operations.</p>
                </div>
            </div>

            <div class="mini-card-body">
                <div class="quick-grid">
                    <a href="{{ route('admin.batch-fee-plans.index') }}" class="quick-link">
                        <strong>Batch Fee Plans</strong>
                        <small>Create and manage batch-wise fee structure.</small>
                    </a>

                    <a href="{{ route('admin.batch-fee-plans.create') }}" class="quick-link">
                        <strong>Add Fee Plan</strong>
                        <small>Map new fee plan to a batch.</small>
                    </a>

                    <a href="{{ route('admin.fee-collections.index') }}" class="quick-link">
                        <strong>Collections</strong>
                        <small>View all receipts and payments.</small>
                    </a>

                    <a href="{{ route('admin.fee-collections.create') }}" class="quick-link">
                        <strong>Receive Fee</strong>
                        <small>Collect payment and print receipt.</small>
                    </a>
                </div>
            </div>
        </div>

        <div class="mini-card">
            <div class="mini-card-head">
                <div>
                    <h3>Recent Payments</h3>
                    <p>Latest received fee payments.</p>
                </div>
            </div>

            <div class="mini-card-body">
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Receipt</th>
                                <th>Amount</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($recentPayments as $payment)
                                <tr>
                                    <td>
                                        <strong>{{ $payment->receipt_no }}</strong>
                                        <br>
                                        <small style="color:#64748b;">
                                            {{ optional($payment->student)->name ?: '-' }}
                                        </small>
                                    </td>

                                    <td>
                                        <span class="amount-paid">
                                            ₹{{ number_format($payment->amount, 2) }}
                                        </span>
                                        <br>
                                        <small style="color:#64748b;">
                                            {{ $payment->payment_date ? $payment->payment_date->format('d M Y') : '-' }}
                                        </small>
                                    </td>

                                    <td>
                                        <a href="{{ route('admin.fee-collections.receipt', $payment) }}" class="btn btn-light">
                                            Receipt
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align:center;padding:30px;color:#64748b;">
                                        No payments found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </aside>
</div>

@endsection