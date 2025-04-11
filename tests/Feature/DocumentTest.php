<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Document;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
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
    $role = Role::create([
        'name'=> 'pédagogie',
    ]);
    $user->roles()->attach($role);

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
    $this->actingAs($user);
    $response = $this->get(route('documents.index'));

    // Assert
    $response->assertStatus(200);
    $response->assertSee('Category 1');
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
        $response = $this->get(route('create-documents'));

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
        $user->roles()->attach($role);

        $category = Category::create(['name' => 'Category 1','role_id' => $role->id]);
        $data = [
            'name' => 'Document 1',
            'content' => 'Content 1 that could be markdown file',
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
    public function test_show_displays_document_when_user_is_author()
    {
        $user = User::create([
            "name"=> "John Doe",
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);
        $this->actingAs($user);
        $role = Role::create(['name'=> 'pédagogie']);
        //$user->roles()->attach($role);

        $category = Category::create(['name' => 'Category 1','role_id' => $role->id]);

        $document = Document::create([
            'name' => 'Document 1',
            'content' => 'Content 1',
            'excerpt' => 'Excerpt 1',
            'created_by' => $user->id,
        ]);
        $document->categories()->attach($category->id);

        // Act
        $response = $this->get(route('documents.show', $document->id));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('document', $document);
    }

    public function test_show_displays_document_when_user_got_proper_role()
    {

        $user = User::create([
            "name"=> "John Doe",
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);
        $author = User::create([
            "name"=> "Jane Doe",
            'email' => 'jane.doe@example.com',
            'password' => bcrypt('password123'),
        ]);
        $this->actingAs($author);
        $role = Role::create(['name'=> 'pédagogie']);
        $author->roles()->attach($role);
        $user->roles()->attach($role);

        $category = Category::create(['name' => 'Category 1','role_id' => $role->id]);

        $document = Document::create([
            'name' => 'Document 1',
            'content' => 'Content 1',
            'excerpt' => 'Excerpt 1',
            'created_by' => $author->id,
        ]);
        $document->categories()->attach($category->id);

        // Act
        $this->actingAs($user);
        $response = $this->get(route('documents.show', $document->id));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('document', $document);
    }
}
