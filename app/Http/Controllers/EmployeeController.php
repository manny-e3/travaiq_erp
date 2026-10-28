<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\EmployeeContract;
use App\Models\EmployeeDocument;
use App\Models\EmploymentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['department', 'profile', 'designation']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('job_title', 'like', "%{$search}%")
                  ->orWhereHas('designation', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $employees = $query->paginate(10)->withQueryString();
        $departments = Department::all();

        return view('admin.hr.employees.index', compact('employees', 'departments'));
    }

    public function show(User $user)
    {
        $user->load(['profile', 'contracts', 'documents', 'employmentHistories.department', 'employmentHistories.designation', 'department', 'designation']);
        $departments = Department::all();
        $designations = \App\Models\Designation::all();
        
        return view('admin.hr.employees.show', compact('user', 'departments', 'designations'));
    }

    public function updateProfile(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'designation_id' => 'nullable|exists:designations,id',
            'job_title' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'marital_status' => 'nullable|string|in:Single,Married,Divorced,Widowed',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
            
            // Qualifications lists
            'education' => 'nullable|array',
            'education.*.school' => 'required|string|max:255',
            'education.*.degree' => 'required|string|max:255',
            'education.*.year' => 'required|integer|min:1900|max:'.(date('Y') + 5),
            
            'certifications' => 'nullable|array',
            'certifications.*.name' => 'required|string|max:255',
            'certifications.*.issuer' => 'required|string|max:255',
            'certifications.*.year' => 'required|integer|min:1900|max:'.(date('Y') + 5),
            
            'skills' => 'nullable|array',
            'skills.*' => 'required|string|max:255',
            
            'languages' => 'nullable|array',
            'languages.*' => 'required|string|max:255',
        ]);

        // Update basic User details
        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'designation_id' => $validated['designation_id'] ?? null,
            'job_title' => isset($validated['designation_id']) ? (\App\Models\Designation::find($validated['designation_id'])?->name) : ($validated['job_title'] ?? null),
            'department_id' => $validated['department_id'] ?? null,
        ]);

        // Update or create Profile details
        $profileData = [
            'gender' => $validated['gender'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'marital_status' => $validated['marital_status'] ?? null,
            'address' => $validated['address'] ?? null,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
            'education' => $validated['education'] ?? [],
            'certifications' => $validated['certifications'] ?? [],
            'skills' => $validated['skills'] ?? [],
            'languages' => $validated['languages'] ?? [],
        ];

        $user->profile()->updateOrCreate([], $profileData);

        return redirect()->back()->with('success', 'Employee profile updated successfully.');
    }

    public function addContract(Request $request, User $user)
    {
        $validated = $request->validate([
            'contract_type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'basic_salary' => 'required|numeric|min:0',
            'status' => 'required|string|in:Active,Expired,Terminated,Pending',
            'notes' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,png,zip|max:5120', // 5MB limit
        ]);

        $documentPath = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            // Store file in public disk, under employee_contracts directory
            $fileName = time() . '_' . $file->getClientOriginalName();
            $documentPath = $file->storeAs('employee_contracts', $fileName, 'public');
        }

        // If status is Active, set all other contracts for this user to Expired
        if ($validated['status'] === 'Active') {
            $user->contracts()->where('status', 'Active')->update(['status' => 'Expired']);
        }

        $user->contracts()->create([
            'contract_type' => $validated['contract_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'basic_salary' => $validated['basic_salary'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
            'document_path' => $documentPath,
        ]);

        return redirect()->back()->with('success', 'Contract added successfully.');
    }

    public function addDocument(Request $request, User $user)
    {
        $validated = $request->validate([
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|string|in:ID Card,Certificate,CV,Contract,Other',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,zip|max:5120',
            'notes' => 'nullable|string',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('employee_vault/' . $user->id, $fileName, 'public');
        }

        $user->documents()->create([
            'document_name' => $validated['document_name'],
            'document_type' => $validated['document_type'],
            'file_path' => $filePath,
            'notes' => $validated['notes'],
        ]);

        return redirect()->back()->with('success', 'Document uploaded to vault successfully.');
    }

    public function addHistory(Request $request, User $user)
    {
        $validated = $request->validate([
            'designation_id' => 'nullable|exists:designations,id',
            'job_title' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'basic_salary' => 'nullable|numeric|min:0',
            'change_reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if (isset($validated['designation_id'])) {
            $validated['job_title'] = \App\Models\Designation::find($validated['designation_id'])?->name;
        }

        // Create employment history entry
        $user->employmentHistories()->create($validated);

        // Optionally update the current user's job title and department
        $user->update([
            'designation_id' => $validated['designation_id'] ?? null,
            'job_title' => $validated['job_title'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Employment history updated successfully.');
    }
}
