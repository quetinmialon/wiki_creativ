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

    public function addToFavorite(Request $request, $documentId): JsonResponse
    {
        if ($this->favoriteService->addToFavorites($documentId)) {
            return response()->json(['message' => 'Ajouté aux favoris.'], 200);
        }
        return response()->json(['message' => 'Document introuvable.'], 404);
    }

    public function removeFromFavorite(Request $request, $documentId): JsonResponse
    {
        $response = $this->favoriteService->removeFromFavorites($documentId);
        if (!$response) {
            return response()->json(['message' => 'Document introuvable.'], 404);
        }
        return response()->json(['message' => 'Retiré des favoris.'], 200);
    }
}
