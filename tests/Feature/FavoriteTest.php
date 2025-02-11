<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Document;
use App\Models\Favorite;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use DatabaseTransactions, WithFaker;
    /**
     * A basic feature test example.
     */
    public function test_add_to_favorite_adds_document_to_user_favorites()
    {
        //arange
        $user = User::create([
            "name"=> "John Doe",
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);
        $this->actingAs($user);
        $role = Role::create(['name'=> 'pédagogie']);

        $category = Category::create(['name' => 'Category 1','role_id' => $role->id]);

        $document = Document::create([
            'name' => 'Document 1',
            'content' => 'Content 1',
            'excerpt' => 'Excerpt 1',
            'created_by' => $user->id,
            'categories_id' => [$category->id],
        ]);

        // Act
        $response = $this->post(route('documents.favorite', $document->id));

        // Assert
        $response->assertRedirect();
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);
    }
    public function test_user_can_view_own_favorites()
    {
        // Arrange
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->actingAs($user);
        $role = Role::create(['name'=> 'pédagogie']);

        $category = Category::create(['name' => 'Category 1','role_id' => $role->id]);

        $document1 = Document::create([
            'name' => 'Document 1',
            'content' => 'Content of Document 1',
            'excerpt' => 'Excerpt of Document 1',
            'created_by' => $user->id,
            'categories_id' => [$category->id],
        ]);

        $document2 = Document::create([
            'name' => 'Document 2',
            'content' => 'Content of Document 2',
            'excerpt' => 'Excerpt of Document 2',
            'created_by' => $user->id,
            'categories_id' => [$category->id],
        ]);

        Favorite::create(['user_id' => $user->id, 'document_id' => $document1->id]);
        Favorite::create(['user_id' => $user->id, 'document_id' => $document2->id]);



        // Act
        $response = $this->get(route('documents.favorites'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('favorites', function ($favorites) use ($document1, $document2) {
            return $favorites->contains($document1) && $favorites->contains($document2);
        });
    }

    public function test_user_cannot_view_others_favorites()
    {
        // Arrange
        $user1 = User::create([
            'name' => 'User One',
            'email' => 'user.one@example.com',
            'password' => bcrypt('password123'),
        ]);

        $user2 = User::create([
            'name' => 'User Two',
            'email' => 'user.two@example.com',
            'password' => bcrypt('password123'),
        ]);

        $document1 = Document::create([
            'name' => 'Document 1',
            'content' => 'Content of Document 1',
            'excerpt' => 'Excerpt of Document 1',
            'created_by' => $user1->id,
        ]);

        Favorite::create(['user_id' => $user1->id, 'document_id' => $document1->id]);

        $this->actingAs($user2);

        // Act
        $response = $this->get(route('documents.favorites'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('favorites', function ($favorites) {
            return $favorites->isEmpty();
        });
    }
    public function test_user_can_remove_favorite_document()
    {
        // Arrange
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);

        $document = Document::create([
            'name' => 'Document 1',
            'content' => 'Content of Document 1',
            'excerpt' => 'Excerpt of Document 1',
            'created_by' => $user->id,
        ]);

        Favorite::create(['user_id' => $user->id, 'document_id' => $document->id]);

        $this->actingAs($user);

        // Act
        $response = $this->delete(route('documents.removeFavorite', $document->id));

        // Assert
        $response->assertRedirect();
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);
    }
    public function test_removing_nonexistent_favorite_document()
    {
        // Arrange
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->actingAs($user);

        // Act
        $response = $this->delete(route('documents.removeFavorite', 999)); // ID inexistant

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Document introuvable.');
    }
}
