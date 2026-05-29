<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BatchFeePlan;
use App\Models\FeePayment;
use App\Models\StudentFeeAssignment;
use Illuminate\Support\Facades\DB;

class FeeDashboardController extends Controller
{
    public function index()
    {
        $today = now()->format('Y-m-d');

        $todayCollection = FeePayment::whereDate('payment_date', $today)
            ->where('status', 'paid')
            ->sum('amount');

        $monthCollection = FeePayment::whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->where('status', 'paid')
            ->sum('amount');

        $totalCollection = FeePayment::where('status', 'paid')
            ->sum('amount');

        $totalPending = StudentFeeAssignment::where('status', 'active')
            ->sum('balance_amount');

        $activeFeePlans = BatchFeePlan::where('status', 'active')->count();

        $activeAssignments = StudentFeeAssignment::where('status', 'active')->count();

        $pendingStudents = StudentFeeAssignment::where('status', 'active')
            ->where('balance_amount', '>', 0)
            ->count();

        $recentPayments = FeePayment::with(['student', 'batch', 'assignment.feePlan'])
            ->latest()
            ->take(10)
            ->get();

        $pendingFees = StudentFeeAssignment::with(['student', 'batch', 'feePlan'])
            ->where('status', 'active')
            ->where('balance_amount', '>', 0)
            ->orderByDesc('balance_amount')
            ->take(10)
            ->get();

        $batchFeeStats = StudentFeeAssignment::query()
            ->select(
                'batch_id',
                DB::raw('COUNT(*) as total_students'),
                DB::raw('SUM(total_amount) as total_fee'),
                DB::raw('SUM(paid_amount) as total_paid'),
                DB::raw('SUM(balance_amount) as total_pending')
            )
            ->with('batch')
            ->groupBy('batch_id')
            ->orderByDesc('total_pending')
            ->take(8)
            ->get();

        return view('admin.fees.index', compact(
            'todayCollection',
            'monthCollection',
            'totalCollection',
            'totalPending',
            'activeFeePlans',
            'activeAssignments',
            'pendingStudents',
            'recentPayments',
            'pendingFees',
            'batchFeeStats'
        ));
    }
}