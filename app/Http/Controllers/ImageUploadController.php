<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ImageUploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Stocke l'image dans le disque "public" => storage/app/public/documents/images
        $path = $request->file('image')->store('documents/images', 'public');

        // Retourne l'URL correcte pour accéder à l'image
        return response()->json(['url' => Storage::url($path)]);
    }
}
