<?php

use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ImageUploadController;

Route::post('/favorites/{documentId}', [FavoriteController::class, 'ToggleFavorite']);
Route::post('/upload-image', [ImageUploadController::class, 'store']);
