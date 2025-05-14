<?php

namespace App\Services;

use App\Models\Log;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LogService
{
    public function getDocumentLogs($documentId)
    {
        return Document::with('logs')->find($documentId) ?? null;
    }

    public function addLog($documentId)
    {
        $document = Document::find($documentId);

        if (!$document) {
            return null; // Retourne null si le document n'existe pas
        }

        $user = Auth::user();

        return Log::create([
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);
    }

    public function logDocumentAccess($documentId)
    {
        Log::create([
            'user_id' => Auth::id(),
            'document_id' => $documentId,
        ]);
    }
    public function getAllLogs($perPage = 100)
    {
        return Log::orderBy('created_at', 'desc')->paginate($perPage);
    }
    public function getUserLogs($userId)
    {
        return User::with('logs')->find($userId) ?? null;
    }
    public function getLastOpenedDocuments($limit = 5)
    {
        return Log::with('document')
            ->where('user_id', Auth::id())
            ->latest('created_at')
            ->get()
            ->unique('document_id') // supprime les doublons par document
            ->take($limit);
    }

}
