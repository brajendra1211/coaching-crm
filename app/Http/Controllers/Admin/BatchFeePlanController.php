<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchFeePlan;
use App\Models\StudentFeeAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BatchFeePlanController extends Controller
{
    public function index(Request $request)
    {
        $query = BatchFeePlan::with('batch')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('billing_type', 'like', '%' . $request->search . '%')
                    ->orWhereHas('batch', function ($batchQuery) use ($request) {
                        $batchQuery->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('code', 'like', '%' . $request->search . '%');
                    });
            });
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $plans = $query->paginate(15)->withQueryString();

        $batches = Batch::orderBy('name')->get();

        return view('admin.fees.batch-plans.index', compact('plans', 'batches'));
    }

    public function create()
    {
        $plan = new BatchFeePlan([
            'billing_type' => 'monthly',
            'due_day' => 10,
            'fine_per_day' => 0,
            'status' => 'active',
            'effective_from' => now(),
        ]);

        $batches = Batch::where('status', 'active')->orderBy('name')->get();

        return view('admin.fees.batch-plans.create', compact('plan', 'batches'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($request, $data) {
            if (($data['status'] ?? 'active') === 'active') {
                BatchFeePlan::where('batch_id', $data['batch_id'])
                    ->where('status', 'active')
                    ->update(['status' => 'inactive']);
            }

            $plan = BatchFeePlan::create($data);

            if ($request->boolean('apply_to_existing_students')) {
                $this->applyPlanToExistingStudents($plan);
            }
        });

        return redirect()
            ->route('admin.batch-fee-plans.index')
            ->with('success', 'Batch fee plan created successfully.');
    }

    public function edit(BatchFeePlan $batchFeePlan)
    {
        $plan = $batchFeePlan;

        $batches = Batch::where('status', 'active')
            ->orWhere('id', $plan->batch_id)
            ->orderBy('name')
            ->get();

        return view('admin.fees.batch-plans.edit', compact('plan', 'batches'));
    }

    public function update(Request $request, BatchFeePlan $batchFeePlan)
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($request, $data, $batchFeePlan) {
            if (($data['status'] ?? 'active') === 'active') {
                BatchFeePlan::where('batch_id', $data['batch_id'])
                    ->where('id', '!=', $batchFeePlan->id)
                    ->where('status', 'active')
                    ->update(['status' => 'inactive']);
            }

            $batchFeePlan->update($data);

            if ($request->boolean('apply_to_existing_students')) {
                $this->applyPlanToExistingStudents($batchFeePlan);
            }
        });

        return redirect()
            ->route('admin.batch-fee-plans.index')
            ->with('success', 'Batch fee plan updated successfully.');
    }

    public function destroy(BatchFeePlan $batchFeePlan)
    {
        $batchFeePlan->delete();

        return redirect()
            ->route('admin.batch-fee-plans.index')
            ->with('success', 'Batch fee plan deleted successfully.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'batch_id' => ['required', 'integer', 'exists:batches,id'],
            'title' => ['required', 'string', 'max:255'],
            'billing_type' => ['required', 'in:monthly,one_time,installment'],

            'registration_fee' => ['nullable', 'numeric', 'min:0'],
            'admission_fee' => ['nullable', 'numeric', 'min:0'],
            'tuition_fee' => ['nullable', 'numeric', 'min:0'],
            'exam_fee' => ['nullable', 'numeric', 'min:0'],
            'material_fee' => ['nullable', 'numeric', 'min:0'],
            'other_fee' => ['nullable', 'numeric', 'min:0'],

            'due_day' => ['nullable', 'integer', 'min:1', 'max:31'],
            'fine_per_day' => ['nullable', 'numeric', 'min:0'],

            'effective_from' => ['nullable', 'date'],
            'effective_to' => ['nullable', 'date'],

            'notes' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }

    private function applyPlanToExistingStudents(BatchFeePlan $plan): void
    {
        $batch = Batch::with('students')->find($plan->batch_id);

        if (!$batch) {
            return;
        }

        foreach ($batch->students as $student) {
            $this->createOrUpdateFeeAssignment($student->id, $batch->id, $plan);
        }
    }

    public function createOrUpdateFeeAssignment(int $studentId, int $batchId, BatchFeePlan $plan): void
    {
        $total = (float) $plan->registration_fee
            + (float) $plan->admission_fee
            + (float) $plan->tuition_fee
            + (float) $plan->exam_fee
            + (float) $plan->material_fee
            + (float) $plan->other_fee;

        $nextDueDate = $this->calculateNextDueDate($plan->due_day);

        StudentFeeAssignment::updateOrCreate(
            [
                'student_id' => $studentId,
                'batch_id' => $batchId,
                'batch_fee_plan_id' => $plan->id,
            ],
            [
                'billing_type' => $plan->billing_type,

                'registration_fee' => $plan->registration_fee ?? 0,
                'admission_fee' => $plan->admission_fee ?? 0,
                'tuition_fee' => $plan->tuition_fee ?? 0,
                'exam_fee' => $plan->exam_fee ?? 0,
                'material_fee' => $plan->material_fee ?? 0,
                'other_fee' => $plan->other_fee ?? 0,

                'total_amount' => $total,
                'paid_amount' => 0,
                'discount_amount' => 0,
                'fine_amount' => 0,
                'balance_amount' => $total,

                'due_day' => $plan->due_day,
                'next_due_date' => $nextDueDate,
                'assigned_at' => now()->format('Y-m-d'),
                'status' => 'active',
            ]
        );
    }

    private function calculateNextDueDate(?int $dueDay): ?string
    {
        if (!$dueDay) {
            return null;
        }

        $today = now();
        $day = min($dueDay, $today->daysInMonth);

        $date = Carbon::create($today->year, $today->month, $day);

        if ($date->isPast() && !$date->isToday()) {
            $nextMonth = $today->copy()->addMonth();
            $day = min($dueDay, $nextMonth->daysInMonth);

            $date = Carbon::create($nextMonth->year, $nextMonth->month, $day);
        }

        return $date->format('Y-m-d');
    }
}