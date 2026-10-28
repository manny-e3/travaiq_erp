<?php

namespace App\Http\Controllers;

use App\Models\EmployeeDocument;
use Illuminate\Http\Request;

class DocumentVaultController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeDocument::with(['user.department']);

        // Search by employee or document name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('document_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQ) use ($search) {
                      $userQ->where('name', 'like', "%{$search}%")
                            ->orWhere('employee_id', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by document type
        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        $documents = $query->latest()->paginate(20)->withQueryString();

        // Stats
        $stats = [
            'total' => EmployeeDocument::count(),
            'cv' => EmployeeDocument::where('document_type', 'CV')->count(),
            'id_card' => EmployeeDocument::where('document_type', 'ID Card')->count(),
            'certificate' => EmployeeDocument::where('document_type', 'Certificate')->count(),
            'other' => EmployeeDocument::where('document_type', 'Other')->count() + EmployeeDocument::where('document_type', 'Contract')->count(),
        ];

        return view('admin.hr.documents.index', compact('documents', 'stats'));
    }
}
