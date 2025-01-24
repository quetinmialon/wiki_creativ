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
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    /**
     * Test de la méthode index.
     */
    public function test_index_displays_categories_and_documents()
    {
        // Arrange
        $user = User::create([
            "name"=> "John Doe",
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);
        $this->actingAs($user);
        $role = Role::create([
            'name'=> 'pédagogie',
        ]);

        $category = Category::create([
            'name' => 'Category 1',
            'role_id' => $role->id
        ]);
        $document = Document::create([
            'name' => 'Document 1',
            'content' => 'Content 1',
            'excerpt' => 'Excerpt 1',
            'created_by' => $user->id,
        ]);
        $category->documents()->attach($document->id);

        // Act
        $response = $this->get(route('documents.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('categories', function ($categories) use ($category) {
            return $categories->contains($category);
        });
    }

    /**
     * Test de la méthode create.
     */
    public function test_create_displays_form_with_roles()
    {
        // Arrange
        $user = User::create([
            "name"=> "John Doe",
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);
        $this->actingAs($user);

        // Act
        $response = $this->get(route('documents.create'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('roles');
    }

    /**
     * Test de la méthode store.
     */
    public function test_store_creates_new_document()
    {
        // Arrange
        $user = User::create([
            "name"=> "John Doe",
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);
        $this->actingAs($user);
        $role = Role::create(['name'=> 'pédagogie']);

        $category = Category::create(['name' => 'Category 1','role_id' => $role->id]);
        $data = [
            'name' => 'Document 1',
            'content' => 'Content 1',
            'excerpt' => 'Excerpt 1',
            'categories_id' => [$category->id],
        ];

        // Act
        $response = $this->post(route('documents.store'), $data);

        // Assert
        $response->assertRedirect(route('documents.index'));
        $this->assertDatabaseHas('documents', ['name' => 'Document 1']);
        $this->assertDatabaseHas('category_document', [
            'category_id' => $category->id,
        ]);
    }

    /**
     * Test de la méthode show.
     */
    public function test_show_displays_document()
    {
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
        $response = $this->get(route('documents.show', $document->id));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('document', $document);
    }

    /**
     * Test de la méthode destroy.
     */
    public function test_destroy_deletes_document()
    {
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
        $response = $this->delete(route('documents.destroy', $document->id));

        // Assert
        $response->assertRedirect(route('documents.index'));
        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
    }
}
