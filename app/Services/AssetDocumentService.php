<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssetDocumentService
{
    public function storeDocument(Asset $asset, array $data, $file, int $userId): AssetDocument
    {
        // Create a safe, unique filename
        $filename = time() . '_' . Str::slug($file->getClientOriginalName());
        
        // Store file in public disk under 'asset-documents' folder
        $path = $file->storeAs('asset-documents', $filename, 'public');

        return $asset->documents()->create([
            'document_type' => $data['document_type'],
            'title' => $data['title'],
            'file_path' => $path,
            'uploaded_by' => $userId,
        ]);
    }

    public function deleteDocument(AssetDocument $assetDocument): bool
    {
        // Check if file exists and delete it
        if (Storage::disk('public')->exists($assetDocument->file_path)) {
            Storage::disk('public')->delete($assetDocument->file_path);
        }

        return $assetDocument->delete();
    }
}
