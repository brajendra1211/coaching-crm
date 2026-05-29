<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoachingSetting;
use App\Models\Course;
use App\Models\Lead;
use App\Models\SeoLocation;
use App\Models\WebsitePage;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'courses' => $this->safeCount(Course::class, 'courses'),
            'leads' => $this->safeCount(Lead::class, 'leads'),
            'today_leads' => $this->safeTodayCount(Lead::class, 'leads'),
            'contact_enquiries' => $this->safeWhereCount(Lead::class, 'leads', 'source', 'website_contact'),
            'pages' => $this->safeCount(WebsitePage::class, 'website_pages'),
            'locations' => $this->safeCount(SeoLocation::class, 'seo_locations'),
        ];

        $recentLeads = collect();

        if (class_exists(Lead::class) && Schema::hasTable('leads')) {
            $recentLeads = Lead::with('course')
                ->latest()
                ->take(6)
                ->get();
        }

        $setting = null;

        if (class_exists(CoachingSetting::class) && Schema::hasTable('coaching_settings')) {
            $setting = CoachingSetting::current();
        }

        return view('admin.dashboard', compact('stats', 'recentLeads', 'setting'));
    }

    private function safeCount(string $modelClass, string $table): int
    {
        if (!class_exists($modelClass) || !Schema::hasTable($table)) {
            return 0;
        }

        return $modelClass::count();
    }

    private function safeTodayCount(string $modelClass, string $table): int
    {
        if (!class_exists($modelClass) || !Schema::hasTable($table)) {
            return 0;
        }

        return $modelClass::whereDate('created_at', today())->count();
    }

    private function safeWhereCount(string $modelClass, string $table, string $column, string $value): int
    {
        if (!class_exists($modelClass) || !Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
            return 0;
        }

        return $modelClass::where($column, $value)->count();
    }
}
