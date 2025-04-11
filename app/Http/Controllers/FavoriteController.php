<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FavoriteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    protected $favoriteService;

    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }

    public function toggleFavorite(Request $request, $documentId): JsonResponse {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['message' => 'Utilisateur non authentifié'], 401);
        }

        if ($this->favoriteService->isFavorited($documentId, $userId)) {
            $this->favoriteService->removeFromFavorites($documentId, $userId);
            return response()->json(['message' => 'Retiré des favoris avec succès', 'favorited' => false], 200);
        }

        $this->favoriteService->addToFavorites($documentId, $userId);
        return response()->json(['message' => 'Ajouté aux favoris avec succès', 'favorited' => true], 200);
    }
}
