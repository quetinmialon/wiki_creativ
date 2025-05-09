<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
});

test('uploads a valid image and returns url', function () {
    $file = UploadedFile::fake()->image('photo.jpg', 600, 600)->size(500);

    $response = $this->postJson('/api/upload-image', [
        'image' => $file,
    ]);

    $response->assertStatus(200)
             ->assertJsonStructure(['url']);

    // Assert the file was stored
    Storage::disk('public')->assertExists('documents/images/' . $file->hashName());

    // Assert URL correct
    $url = Storage::url('documents/images/' . $file->hashName());
    $this->assertEquals($url, $response->json('url'));
});

test('fails when no file provided', function () {
    $response = $this->postJson('/api/upload-image', []);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['image']);
});

test('fails when file is not an image', function () {
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $response = $this->postJson('/api/upload-image', [
        'image' => $file,
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['image']);
});

test('fails when image exceeds max size', function () {
    // 2049 KB > 2048 KB limit
    $file = UploadedFile::fake()->image('big.png')->size(3000);

    $response = $this->postJson('/api/upload-image', [
        'image' => $file,
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['image']);
});

test('fails when image has invalid mime type', function () {
    $file = UploadedFile::fake()->create('fake.txt', 100);

    $response = $this->postJson('/api/upload-image', [
        'image' => $file,
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['image']);
});
