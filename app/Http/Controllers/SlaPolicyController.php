<?php

namespace App\Http\Controllers;

use App\Models\SlaPolicy;
use Illuminate\Http\Request;

class SlaPolicyController extends Controller
{
    /**
     * Display a listing of SLA policies.
     */
    public function index()
    {
        $policies = SlaPolicy::orderBy('customer_tier')
            ->orderBy('priority')
            ->get();

        return view('admin.helpdesk.sla_policies', compact('policies'));
    }

    /**
     * Store a newly created SLA policy.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'customer_tier' => 'required|in:Standard,Gold,Platinum',
            'category' => 'nullable|in:Technical,Billing,Account,Feedback,General',
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'response_time_hours' => 'required|numeric|min:0.01',
            'resolution_time_hours' => 'required|numeric|min:0.01',
        ]);

        SlaPolicy::create($validated);

        return redirect()->route('admin.sla-policies.index')
            ->with('success', 'SLA Policy created successfully.');
    }

    /**
     * Update the specified SLA policy.
     */
    public function update(Request $request, SlaPolicy $slaPolicy)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'customer_tier' => 'required|in:Standard,Gold,Platinum',
            'category' => 'nullable|in:Technical,Billing,Account,Feedback,General',
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'response_time_hours' => 'required|numeric|min:0.01',
            'resolution_time_hours' => 'required|numeric|min:0.01',
            'is_active' => 'required|boolean',
        ]);

        $slaPolicy->update($validated);

        return redirect()->route('admin.sla-policies.index')
            ->with('success', 'SLA Policy updated successfully.');
    }

    /**
     * Remove the specified SLA policy.
     */
    public function destroy(SlaPolicy $slaPolicy)
    {
        $slaPolicy->delete();

        return redirect()->route('admin.sla-policies.index')
            ->with('success', 'SLA Policy deleted successfully.');
    }
}
