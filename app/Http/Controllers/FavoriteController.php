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

    public function ToggleFavorite(Request $request, $documentId): JsonResponse
    {
        if ($this->favoriteService->isFavorited($documentId, $request->userId)) {
            $this->favoriteService->removeFromFavorites($documentId);
            return response()->json(['message' => 'Retiré des favoris avec succès'], 200);
        }
        $this->favoriteService->addToFavorites($documentId);
        return response()->json(['message' => 'Ajouté aux favoris avec succès'], 200);
    }
}
