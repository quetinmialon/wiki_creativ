<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageUploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif'
        ]);
        $path = $request->file('image')->store('documents/images', 'public');
        return response()->json(['url' => asset("storage/$path")]);
    }
}
