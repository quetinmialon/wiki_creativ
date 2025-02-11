<?php

namespace App\Services;

use App\Models\Favorite;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;

class FavoriteService
{
    public function addToFavorites($documentId)
    {
        $user = Auth::user();
        if (!$user || !Document::find($documentId)) {
            return false;
        }

        $alreadyFavorite = Favorite::where('user_id', $user->id)
                                   ->where('document_id', $documentId)
                                   ->exists();
        if (!$alreadyFavorite) {
            Favorite::create(['user_id' => $user->id, 'document_id' => $documentId]);
        }

        return true;
    }

    public function getUserFavorites()
    {
        return Document::whereIn('id', Auth::user()->favorites->pluck('document_id'))->get();
    }


    public function removeFromFavorites($documentId)
    {
        $document = Favorite::where('user_id', Auth::id())->where('document_id', $documentId);
        if (!$document){
            return false;
        }
        return $document->delete();
    }
}
