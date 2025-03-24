<?php

use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ImageUploadController;

Route::post('/api/favorites/{documentId}', [FavoriteController::class, 'addToFavorite']);
Route::delete('/api/favorites/{documentId}', [FavoriteController::class, 'removeFromFavorite']);
Route::post('/upload-image', [ImageUploadController::class, 'store']);
