<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin\Payment;
use App\Models\SuperAdmin\Plan;
use App\Models\SuperAdmin\Subscription;
use App\Models\SuperAdmin\Tenant;

class DashboardController extends Controller
{
    public function index()
    {
        return view('super-admin.dashboard', [
            'totalTenants' => Tenant::count(),
            'activeTenants' => Tenant::where('status', 'active')->count(),
            'expiredTenants' => Tenant::where('status', 'expired')
                ->orWhereDate('subscription_ends_at', '<', now()->toDateString())
                ->count(),
            'plansCount' => Plan::where('status', 'active')->count(),
            'monthlyRevenue' => Payment::where('status', 'paid')
                ->whereYear('payment_date', now()->year)
                ->whereMonth('payment_date', now()->month)
                ->sum('amount'),
            'expiringSoon' => Tenant::with('plan')
                ->whereDate('subscription_ends_at', '>=', now()->toDateString())
                ->whereDate('subscription_ends_at', '<=', now()->addDays(7)->toDateString())
                ->orderBy('subscription_ends_at')
                ->take(10)
                ->get(),
            'recentTenants' => Tenant::with(['plan', 'activeDomain'])->latest()->take(8)->get(),
            'recentSubscriptions' => Subscription::latest()->take(8)->get(),
        ]);
    }
}
