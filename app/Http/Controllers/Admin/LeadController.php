<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::with('course')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $leads = $query->paginate(15)->withQueryString();

        return view('admin.leads.index', compact('leads'));
    }

    public function show(Lead $lead)
    {
        $lead->load('course');

        return view('admin.leads.show', compact('lead'));
    }

    public function update(Request $request, Lead $lead)
    {
        $request->validate([
            'status' => ['required', 'in:new,contacted,interested,not_interested,converted,lost'],
            'follow_up_date' => ['nullable', 'date'],
            'admin_notes' => ['nullable', 'string'],
        ]);

        $lead->update([
            'status' => $request->status,
            'follow_up_date' => $request->follow_up_date,
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()
            ->route('admin.leads.show', $lead)
            ->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()
            ->route('admin.leads.index')
            ->with('success', 'Lead deleted successfully.');
    }
}
