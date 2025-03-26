<?php

namespace App\Services;

use App\Models\Favorite;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;

class FavoriteService
{
    public function addToFavorites($documentId, $userId)
    {
        Favorite::create(['user_id' => $userId, 'document_id' => $documentId]);
    }

    public function getUserFavorites()
    {
        $favorites = Favorite::with('document.author')
            ->where('user_id', Auth::id())
            ->get();

        // Supprimer les favoris dont le document n'existe plus
        $favorites = $favorites->reject(function ($favorite) {
            return is_null($favorite->document);
        });

        return $favorites;
    }

    public function removeFromFavorites($documentId, $userId) {
        $document = Favorite::where('user_id', $userId)->where('document_id', $documentId)->first();
        if (!$document){
            return false;
        }
        return $document->delete();
    }


    public function isFavorited($documentId, $userId)
    {
        return Favorite::where('user_id', $userId)->where('document_id', $documentId)->exists();
    }
}
