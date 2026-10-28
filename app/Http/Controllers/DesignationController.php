<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Services\DesignationService;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    protected $designationService;

    public function __construct(DesignationService $designationService)
    {
        $this->designationService = $designationService;
    }

    public function index()
    {
        $designations = $this->designationService->getAllWithUserCount();
        return view('admin.designations.index', compact('designations'));
    }

    public function create()
    {
        return view('admin.designations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:designations',
            'description' => 'nullable|string',
        ]);

        $this->designationService->createDesignation($validated);

        return redirect()->route('admin.designations.index')->with('success', 'Designation created successfully.');
    }

    public function edit(Designation $designation)
    {
        return view('admin.designations.edit', compact('designation'));
    }

    public function update(Request $request, Designation $designation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:designations,name,' . $designation->id,
            'description' => 'nullable|string',
        ]);

        $this->designationService->updateDesignation($designation, $validated);

        return redirect()->route('admin.designations.index')->with('success', 'Designation updated successfully.');
    }

    public function destroy(Designation $designation)
    {
        if (!$this->designationService->deleteDesignation($designation)) {
            return redirect()->route('admin.designations.index')->with('error', 'Cannot delete designation with assigned users.');
        }

        return redirect()->route('admin.designations.index')->with('success', 'Designation deleted successfully.');
    }
}
