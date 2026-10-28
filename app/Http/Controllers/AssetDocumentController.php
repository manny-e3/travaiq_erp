<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetDocument;
use App\Services\AssetDocumentService;
use Illuminate\Http\Request;

class AssetDocumentController extends Controller
{
    protected $documentService;

    public function __construct(AssetDocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'document_type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'document_file' => 'required|file|max:10240', // 10MB max limit
        ]);

        if ($request->hasFile('document_file')) {
            $this->documentService->storeDocument(
                $asset, 
                $validated, 
                $request->file('document_file'), 
                auth()->id()
            );

            return back()->with('success', 'Document attached to asset successfully.');
        }

        return back()->with('error', 'Failed to upload document.');
    }

    public function destroy(AssetDocument $assetDocument)
    {
        $this->documentService->deleteDocument($assetDocument);

        return back()->with('success', 'Document deleted successfully.');
    }
}
