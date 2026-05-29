<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchFeePlan;
use App\Models\CoachingSetting;
use App\Models\Course;
use App\Models\FeePayment;
use App\Models\InvoiceSetting;
use App\Models\Lead;
use App\Models\SeoLocation;
use App\Models\Student;
use App\Models\StudentFeeAssignment;
use App\Models\Teacher;
use App\Models\WebsitePage;
use Illuminate\Support\Facades\DB;
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
            'teachers' => $this->safeCount(Teacher::class, 'teachers'),
            'students' => $this->safeCount(Student::class, 'students'),
            'batches' => $this->safeCount(Batch::class, 'batches'),
            'batch_fee_plans' => $this->safeCount(BatchFeePlan::class, 'batch_fee_plans'),
            'fee_assignments' => $this->safeCount(StudentFeeAssignment::class, 'student_fee_assignments'),
            'fee_payments' => $this->safeCount(FeePayment::class, 'fee_payments'),
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

        $setupFlow = $this->buildSetupFlow($setting, $stats);

        return view('admin.dashboard', compact('stats', 'recentLeads', 'setting', 'setupFlow'));
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

    private function buildSetupFlow(?CoachingSetting $setting, array $stats): array
    {
        $firstBatchId = null;
        $batchBuilderId = null;
        $firstAssignmentId = null;
        $assignedStudents = 0;
        $activeFeePlans = 0;
        $activeFeeAssignments = 0;
        $invoiceSettingConfigured = false;

        if (class_exists(Batch::class) && Schema::hasTable('batches')) {
            $firstBatchId = Batch::query()->oldest('id')->value('id');
        }

        if (Schema::hasTable('batch_students')) {
            $assignedStudents = DB::table('batch_students')
                ->where('status', 'active')
                ->count();

            $batchBuilderId = DB::table('batch_students')
                ->where('status', 'active')
                ->oldest('batch_id')
                ->value('batch_id');
        }

        $batchBuilderId = $batchBuilderId ?: $firstBatchId;

        if (class_exists(BatchFeePlan::class) && Schema::hasTable('batch_fee_plans')) {
            $activeFeePlans = BatchFeePlan::where('status', 'active')->count();
        }

        if (class_exists(StudentFeeAssignment::class) && Schema::hasTable('student_fee_assignments')) {
            $activeFeeAssignments = StudentFeeAssignment::whereIn('status', ['active', 'paid'])->count();
            $firstAssignmentId = StudentFeeAssignment::whereIn('status', ['active', 'paid'])
                ->oldest('id')
                ->value('id');
        }

        if (class_exists(InvoiceSetting::class) && Schema::hasTable('invoice_settings')) {
            $invoiceSetting = InvoiceSetting::query()->first();

            $invoiceSettingConfigured = $invoiceSetting
                && filled($invoiceSetting->invoice_title)
                && filled($invoiceSetting->invoice_prefix);
        }

        $settingsDone = $this->isInstituteSettingConfigured($setting);
        $hasCourses = ($stats['courses'] ?? 0) > 0;
        $hasTeachers = ($stats['teachers'] ?? 0) > 0;
        $hasStudents = ($stats['students'] ?? 0) > 0;
        $hasBatches = ($stats['batches'] ?? 0) > 0;
        $hasAssignedStudents = $assignedStudents > 0;
        $hasActiveFeePlans = $activeFeePlans > 0;
        $hasFeeAssignments = $activeFeeAssignments > 0;
        $hasFeePayments = ($stats['fee_payments'] ?? 0) > 0;

        $steps = [
            [
                'key' => 'settings',
                'level' => 1,
                'title' => 'Complete institute settings',
                'description' => 'Update the institute name, phone, WhatsApp, email, and address with your real details.',
                'done' => $settingsDone,
                'required' => true,
                'action_label' => 'Complete Settings',
                'action_url' => route('admin.settings.index'),
                'blocked' => false,
                'count' => $settingsDone ? 'Ready' : 'Required',
            ],
            [
                'key' => 'courses',
                'level' => 2,
                'title' => 'Add a course',
                'description' => 'At least one course is required for admissions, batches, and course pages on the website.',
                'done' => $hasCourses,
                'required' => true,
                'action_label' => 'Add Course',
                'action_url' => route('admin.courses.create'),
                'blocked' => false,
                'count' => $stats['courses'] ?? 0,
            ],
            [
                'key' => 'teachers',
                'level' => 3,
                'title' => 'Add a teacher',
                'description' => 'Create teacher records so faculty can be assigned to batches and use the teacher portal.',
                'done' => $hasTeachers,
                'required' => true,
                'action_label' => 'Add Teacher',
                'action_url' => route('admin.teachers.create'),
                'blocked' => false,
                'count' => $stats['teachers'] ?? 0,
            ],
            [
                'key' => 'students',
                'level' => 4,
                'title' => 'Add a student',
                'description' => 'Create student profiles after admission, then assign students to the correct batch.',
                'done' => $hasStudents,
                'required' => true,
                'action_label' => 'Add Student',
                'action_url' => route('admin.students.create'),
                'blocked' => !$hasCourses,
                'blocked_text' => 'Add a course first.',
                'count' => $stats['students'] ?? 0,
            ],
            [
                'key' => 'batches',
                'level' => 5,
                'title' => 'Create a batch',
                'description' => 'Set up a batch with course, timing, teacher, and capacity details.',
                'done' => $hasBatches,
                'required' => true,
                'action_label' => 'Create Batch',
                'action_url' => route('admin.batches.create'),
                'blocked' => !$hasCourses,
                'blocked_text' => 'Add a course before creating a batch.',
                'count' => $stats['batches'] ?? 0,
            ],
            [
                'key' => 'batch_fee_plans',
                'level' => 6,
                'title' => 'Set a batch fee plan',
                'description' => 'Define registration, admission, tuition, and due-date details. Student fees are assigned from this plan.',
                'done' => $hasActiveFeePlans,
                'required' => true,
                'action_label' => 'Create Fee Plan',
                'action_url' => route('admin.batch-fee-plans.create'),
                'blocked' => !$hasBatches,
                'blocked_text' => 'Create a batch before adding a fee plan.',
                'count' => $activeFeePlans,
            ],
            [
                'key' => 'batch_assignment',
                'level' => 7,
                'title' => 'Assign students to a batch',
                'description' => 'Select students in the batch builder. If an active fee plan exists, fee assignments are created automatically.',
                'done' => $hasAssignedStudents,
                'required' => true,
                'action_label' => 'Assign Students',
                'action_url' => $firstBatchId ? route('admin.batches.builder', $firstBatchId) : route('admin.batches.index'),
                'blocked' => !$hasStudents || !$hasBatches,
                'blocked_text' => !$hasStudents ? 'Add a student first.' : 'Create a batch first.',
                'count' => $assignedStudents,
            ],
            [
                'key' => 'fee_assignment',
                'level' => 8,
                'title' => 'Verify fee assignments',
                'description' => 'After students are assigned to a batch, confirm that their fee ledger is ready.',
                'done' => $hasFeeAssignments,
                'required' => true,
                'action_label' => 'Sync In Builder',
                'action_url' => $batchBuilderId ? route('admin.batches.builder', $batchBuilderId) : route('admin.fees.index'),
                'blocked' => !$hasAssignedStudents || !$hasActiveFeePlans,
                'blocked_text' => !$hasActiveFeePlans ? 'Create a batch fee plan first.' : 'Assign students to a batch first.',
                'count' => $activeFeeAssignments,
            ],
            [
                'key' => 'invoice_settings',
                'level' => 9,
                'title' => 'Receipt / invoice settings',
                'description' => 'Set the receipt title, prefix, paper size, and visible receipt fields.',
                'done' => $invoiceSettingConfigured,
                'required' => false,
                'action_label' => 'Invoice Settings',
                'action_url' => route('admin.invoice-settings.edit'),
                'blocked' => false,
                'count' => $invoiceSettingConfigured ? 'Ready' : 'Optional',
            ],
            [
                'key' => 'fee_collection',
                'level' => 10,
                'title' => 'Collect the first fee payment',
                'description' => 'Once fee assignments are ready, collect a payment and generate the receipt.',
                'done' => $hasFeePayments,
                'required' => false,
                'action_label' => 'Collect Fee',
                'action_url' => $firstAssignmentId
                    ? route('admin.fee-collections.create', ['assignment_id' => $firstAssignmentId])
                    : route('admin.fee-collections.create'),
                'blocked' => !$hasFeeAssignments,
                'blocked_text' => 'Prepare fee assignments first.',
                'count' => $stats['fee_payments'] ?? 0,
            ],
        ];

        $requiredSteps = collect($steps)->where('required', true);
        $completedRequired = $requiredSteps->where('done', true)->count();
        $totalRequired = $requiredSteps->count();
        $completedAll = collect($steps)->where('done', true)->count();
        $progress = $totalRequired > 0 ? (int) round(($completedRequired / $totalRequired) * 100) : 100;
        $nextStep = collect($steps)->firstWhere('done', false);

        return [
            'steps' => $steps,
            'next_step' => $nextStep,
            'progress' => $progress,
            'completed_required' => $completedRequired,
            'total_required' => $totalRequired,
            'completed_all' => $completedAll,
            'total_all' => count($steps),
            'has_required_pending' => $completedRequired < $totalRequired,
        ];
    }

    private function isInstituteSettingConfigured(?CoachingSetting $setting): bool
    {
        if (!$setting) {
            return false;
        }

        $requiredFields = [
            $setting->institute_name,
            $setting->phone,
            $setting->whatsapp,
            $setting->email,
            $setting->address,
        ];

        $hasRequiredFields = collect($requiredFields)->every(fn ($value) => filled($value));

        if (!$hasRequiredFields) {
            return false;
        }

        $defaultValues = [
            'institute_name' => 'Edu Institute',
            'phone' => '9999999999',
            'whatsapp' => '919999999999',
            'email' => 'info@coachingcrm.com',
        ];

        foreach ($defaultValues as $field => $defaultValue) {
            if ((string) $setting->{$field} === $defaultValue) {
                return false;
            }
        }

        return true;
    }
}
