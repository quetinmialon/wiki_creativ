<?php

namespace Tests\Unit\Models;

use App\Models\Document;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FavoriteModelTest extends TestCase
{
    public function test_create_favorite()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create();

        $favorite = Favorite::factory()->create([
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);

        $this->assertInstanceOf(Favorite::class, $favorite);
    }
    public function test_favorite_belongs_to_user()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create();
        $favorite = Favorite::factory()->create([
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);

        $this->assertInstanceOf(User::class, $favorite->user);
        $this->assertEquals($user->id, $favorite->user->id);
    }
    public function test_favorite_belongs_to_document()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create();
        $favorite = Favorite::factory()->create([
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);

        $this->assertInstanceOf(Document::class, $favorite->document);
        $this->assertEquals($document->id, $favorite->document->id);
    }
    public function test_favorite_can_be_deleted()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create();
        $favorite = Favorite::factory()->create([
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);
        $this->assertDatabaseHas('favorites', [
            'id' => $favorite->id,
        ]);
        $favorite->delete();

        $this->assertDatabaseMissing('favorites', ['id' => $favorite->id]);
    }
    public function test_favorite_can_be_updated()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create();
        $favorite = Favorite::factory()->create([
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);

        $favorite->update(['updated_at' => now()]);

        $this->assertDatabaseHas('favorites', [
            'id' => $favorite->id,
            'updated_at' => $favorite->updated_at,
        ]);
    }
}
