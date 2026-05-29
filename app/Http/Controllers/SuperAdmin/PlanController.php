<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount('tenants')->latest()->paginate(15);

        return view('super-admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('super-admin.plans.form', ['plan' => new Plan(['status' => 'active'])]);
    }

    public function store(Request $request)
    {
        Plan::create($this->validateData($request));

        return redirect()->route('super-admin.plans.index')->with('success', 'Plan created successfully.');
    }

    public function edit(Plan $plan)
    {
        return view('super-admin.plans.form', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $plan->update($this->validateData($request, $plan->id));

        return redirect()->route('super-admin.plans.index')->with('success', 'Plan updated successfully.');
    }

    public function destroy(Plan $plan)
    {
        $plan->update(['status' => 'inactive']);

        return redirect()->route('super-admin.plans.index')->with('success', 'Plan deactivated successfully.');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        $codeRule = 'unique:sa_plans,code';

        if ($ignoreId) {
            $codeRule .= ',' . $ignoreId;
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:120', $codeRule],
            'monthly_price' => ['nullable', 'numeric', 'min:0'],
            'yearly_price' => ['nullable', 'numeric', 'min:0'],
            'student_limit' => ['nullable', 'integer', 'min:1'],
            'staff_limit' => ['nullable', 'integer', 'min:1'],
            'storage_limit_mb' => ['nullable', 'integer', 'min:1'],
            'features' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $data['code'] = $data['code'] ?: Str::slug($data['name']);
        $data['monthly_price'] = $data['monthly_price'] ?? 0;
        $data['yearly_price'] = $data['yearly_price'] ?? 0;

        return $data;
    }
}
