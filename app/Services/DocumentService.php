<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class DocumentService
{
    public function getAllCategoriesWithDocuments()
    {
        return Category::with(['documents.author'])->get();
    }

    public function createDocument(array $data)
    {
        $data['created_by'] = Auth::id();
        $document = Document::create($data);

        if (!empty($data['categories_id'])) {
            $document->categories()->attach($data['categories_id']);
        }

        return $document;
    }

    public function getDocumentsByCategory($categoryId)
    {
        return Category::findOrFail($categoryId)->documents;
    }

    public function findDocument($id)
    {
        return Document::find($id);
    }

    public function updateDocument(Document $document, array $data)
    {
        $document->update($data);
        $document->categories()->sync($data['categories_id'] ?? []);

        return $document;
    }

    public function deleteDocument(Document $document)
    {
        return $document->delete();
    }
}
