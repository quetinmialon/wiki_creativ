<?php

use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ImageUploadController;

Route::post('/upload-image', [ImageUploadController::class, 'store']);
