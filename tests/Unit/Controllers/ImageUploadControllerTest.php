<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\ImageUploadController;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ImageUploadControllerTest extends TestCase
{
    public function test_store_uploads_image_and_returns_url()
    {
        Storage::fake('public');

        $controller = new ImageUploadController();

        $file = UploadedFile::fake()->image('test.jpg');
        $request = Request::create('/image/upload', 'POST', [], [], ['image' => $file]);

        $response = $controller->store($request);

        $this->assertIsArray($response->getData(true));
        $responseData = $response->getData(true);

        $this->assertArrayHasKey('url', $responseData);
        Storage::disk('public')->assertExists('documents/images/' . $file->hashName());
    }

    public function test_store_throws_validation_exception_if_no_file_provided()
    {
        $this->expectException(ValidationException::class);

        $controller = new ImageUploadController();
        $request = Request::create('/image/upload', 'POST');

        $controller->store($request);
    }

    public function test_store_throws_validation_exception_for_invalid_image_type()
    {
        $this->expectException(ValidationException::class);

        $controller = new ImageUploadController();

        // Création d'un faux fichier .pdf au lieu d'une image
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $request = Request::create('/image/upload', 'POST', [], [], ['image' => $file]);

        $controller->store($request);
    }
}
