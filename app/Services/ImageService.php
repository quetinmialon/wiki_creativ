<?php

namespace App\Services;


use Illuminate\Support\Facades\Storage;
use App\Models\Document;

class ImageService
{
    public function getImagesFromStorage($directory)
    {
        $files = Storage::disk('public')->files($directory); // Liste tous les fichiers du dossier
        $urls = [];

        foreach ($files as $file) {
            $urls[] = Storage::url($file); // Génère l'URL accessible via le symlink
        }

        return $urls;
    }

    public function getUsedImages()
    {
        $documents = Document::all();
        $usedImages = [];

        foreach ($documents as $doc) {
            preg_match_all('/<img[^>]+src="([^">]+)"/', $doc->content, $matches);
            if (!empty($matches[1])) {
                $usedImages = array_merge($usedImages, $matches[1]);
            }
        }

        return array_unique($usedImages);
    }

    public function deleteUnusedImages()
    {
        $directory = 'documents/images';
        $allImages = $this->getImagesFromStorage($directory);
        $usedImages = $this->getUsedImages();

        foreach ($allImages as $imageUrl) {
            // Convertir l'URL en chemin relatif pour Storage
            $filePath = str_replace(Storage::url(''), '', $imageUrl);

            if (!in_array($imageUrl, $usedImages)) {
                Storage::disk('public')->delete($filePath);
            }
        }
    }
}
