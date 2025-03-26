<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FavoriteService;
use Illuminate\Http\JsonResponse;

class FavoriteController extends Controller
{
    protected $favoriteService;

    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }

    public function toggleFavorite(Request $request, $documentId): JsonResponse {
        $validatedData = $request->validate([
            'userId' => 'required|integer'
        ]);

        if ($this->favoriteService->isFavorited($documentId, $validatedData['userId'])) {
            $this->favoriteService->removeFromFavorites($documentId, $validatedData['userId']);
            return response()->json(['message' => 'Retiré des favoris avec succès', 'favorited' => false], 200);
        }

        $this->favoriteService->addToFavorites($documentId, $validatedData['userId']);
        return response()->json(['message' => 'Ajouté aux favoris avec succès', 'favorited' => true], 200);
    }

}
