<?php

use Illuminate\Support\Facades\Storage;
use App\Models\Document;
use App\Services\ImageService;
use Illuminate\Support\Facades\Artisan;


uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->imageService = new ImageService();
    Storage::fake('public');
});

test('it gets images from storage', function () {
    //arrange
    Storage::disk('public')->put('documents/images/test1.jpg', 'fake content');
    Storage::disk('public')->put('documents/images/test2.png', 'fake content');

    //act
    $images = $this->imageService->getImagesFromStorage('documents/images');

    //assert
    expect($images)->toHaveCount(2);
    $this->assertStringContainsString('/storage/documents/images/test1.jpg', $images[0]);
});

test('it detects used images in documents', function () {
    $html = '<p>Image here: <img src="/storage/documents/images/test1.jpg" /></p>';
    Document::factory()->create(['content' => $html]);

    $usedImages = $this->imageService->getUsedImages();

    expect($usedImages)->toContain('/storage/documents/images/test1.jpg');
});

test('it deletes unused images', function () {
    //arrange
    Storage::disk('public')->put('documents/images/test1.jpg', 'fake content');
    Storage::disk('public')->put('documents/images/test2.jpg', 'fake content');
    Document::factory()->create([
        'content' => '<img src="/storage/documents/images/test1.jpg">',
    ]);

    //act
    $this->imageService->deleteUnusedImages();

    //assert
    Storage::disk('public')->assertExists('documents/images/test1.jpg');
    Storage::disk('public')->assertMissing('documents/images/test2.jpg');
});

test('it does not delete used images', function () {
    //arrange
    Storage::disk('public')->put('documents/images/used.jpg', 'fake content');
    Document::factory()->create([
        'content' => '<img src="/storage/documents/images/used.jpg">',
    ]);

    //act
    $this->imageService->deleteUnusedImages();

    //assert
    Storage::disk('public')->assertExists('documents/images/used.jpg');
});

test('command outputs start and end messages', function () {
    // Arrange
    Storage::disk('public')->put('documents/images/unused.png', 'fake content');

    //act
    $output = Artisan::call('temp:deleting-unused-images');

    // Get the output buffer
    $display = Artisan::output();

    // Assert that the output contains the expected info messages
    $this->assertStringContainsString('Début du nettoyage des images...', $display);
    $this->assertStringContainsString('Nettoyage terminé !', $display);
});

test('command deletes unused images', function () {
    // Arrange
    Storage::disk('public')->put('documents/images/unused.png', 'fake content');
    Storage::disk('public')->put('documents/images/used.png', 'fake content');
    Document::factory()->create([
        'content' => '<img src="/storage/documents/images/used.png">',
    ]);

    // Act
    Artisan::call('temp:deleting-unused-images');

    // Assert
    Storage::disk('public')->assertMissing('documents/images/unused.png');
    Storage::disk('public')->assertExists('documents/images/used.png');
});