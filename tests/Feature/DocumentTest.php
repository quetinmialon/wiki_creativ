<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Document;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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
    public function test_document_can_be_see_with_a_permission()
    {
        $user = User::factory()->create();
        $user = User::find($user->id); // Ensure $user is an instance of User
        $author = User::factory()->create();
        $role = Role::create(['name'=> 'pédagogie']);
        $author->roles()->attach($role);
        $document = Document::create([
            'name' => 'Document 1',
            'content' => 'Content 1',
            'excerpt' => 'Excerpt 1',
            'created_by' => $author->id,
        ]);
        $unauthorizedUser = User::factory()->create();
        $unauthorizedUser = User::find($unauthorizedUser->id); // Ensure $unauthorizedUser is an instance of User
        $this->actingAs($unauthorizedUser);
        $response = $this->get(route('documents.show', $document->id));
        $response->assertStatus(403);
        Permission::factory()->create([
            'document_id' => $document->id,
            'expired_at' => now()->addDays(7),
            'status' => 'approved',
            'author' => $user->id,
        ]);
        $this->actingAs($user);
        $response = $this->get(route('documents.show', $document->id));
        $response->assertStatus(200);
        $response->assertViewHas('document', $document);
    }

    public function test_document_can_be_updated_by_superadmin()
    {
        $user = User::factory()->create();
        $user = User::find($user->id); // Ensure $user is an instance of User
        $role = Role::create(['name'=> 'superadmin']);
        $user->roles()->attach($role);
        $category = Category::create(['name' => 'Category 1','role_id' => $role->id]);
        $document = Document::create([
            'name' => 'Document 1',
            'content' => 'Content 1',
            'excerpt' => 'Excerpt 1',
            'created_by' => $user->id,
        ]);
        $document->categories()->attach($category->id);
        $this->actingAs($user);
        $response = $this->put(route('documents.update', $document->id), [
            'name' => 'Updated Document',
            'content' => 'Updated Content',
            'excerpt' => 'Updated Excerpt',
            'categories_id' => [$category->id],
        ]);
        $response->assertRedirect(route('documents.show', $document->id));
        $this->assertDatabaseHas('documents', ['name' => 'Updated Document']);
    }
    public function test_document_can_be_soft_deleted_by_superadmin()
    {
        $user = User::factory()->create();
        $user = User::find($user->id); // Ensure $user is an instance of User
        $role = Role::factory()->create(['name'=> 'superadmin']);
        $user->roles()->attach($role);
        $category = Category::create(['name' => 'Category 1','role_id' => $role->id]);
        $document = Document::create([
            'name' => 'Document 1',
            'content' => 'Content 1',
            'excerpt' => 'Excerpt 1',
            'created_by' => $user->id,
        ]);
        $document->categories()->attach($category->id);
        $this->actingAs($user);
        $response = $this->delete(route('documents.destroy', $document));
        $response->assertRedirect(route('documents.index'));
        $this->assertSoftDeleted('documents', ['name' => 'Document 1']);
    }
    public function test_admin_roles_can_update_document()
    {
        $user = User::factory()->create();
        $user = User::find($user->id); // Ensure $user is an instance of User
        $role = Role::create(['name'=> 'pédagogie']);
        $user->roles()->attach($role);
        $pedagogicUser = User::factory()->create();
        $pedagogicUser = User::find($pedagogicUser->id); // Ensure $pedagogicUser is an instance of User
        $pedagogicUser->roles()->attach($role);
        $category = Category::create(['name' => 'Category 1','role_id' => $role->id]);
        $document = Document::create([
            'name' => 'Document 1',
            'content' => 'Content 1',
            'excerpt' => 'Excerpt 1',
            'created_by' => $user->id,
        ]);
        $document->categories()->attach($category->id);
        $document = Document::with('categories.role')->find($document->id);

        $this->assertNotNull($document->categories->first()->role);

        $adminUser = User::factory()->create();
        $adminUser = User::find($adminUser->id); // Ensure $adminUser is an instance of User
        $adminRole = Role::create(['name'=> "Admin $role->name"]);
        $adminUser->roles()->attach($adminRole);
        $this->actingAs($pedagogicUser);

        $response = $this->put(route('documents.update',$document), [
            'name' => 'Updated Document',
            'content' => 'Updated Content',
            'excerpt' => 'Updated Excerpt',
        ]);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('documents', ['name' => 'Updated Document']);
        $this->assertDatabaseHas('documents', ['name' => 'Document 1']);
        $this->actingAs($adminUser);
        $response = $this->put(route('documents.update', $document->id), [
            'name' => 'Updated Document',
            'content' => 'Updated Content',
            'excerpt' => 'Updated Excerpt',
            'categories_id' => [$category->id],
        ]);
        $response->assertRedirect(route('documents.show', $document->id));
        $this->assertDatabaseHas('documents', ['name' => 'Updated Document']);
        $this->assertDatabaseMissing('documents', ['name' => 'Document 1']);
    }

    public function test_admin_roles_can_delete_document()
    {
        $user = User::factory()->create();
        $user = User::find($user->id); // Ensure $user is an instance of User
        $role = Role::create(['name'=> 'pédagogie']);
        $user->roles()->attach($role);
        $pedagogicUser = User::factory()->create();
        $pedagogicUser = User::find($pedagogicUser->id); // Ensure $pedagogicUser is an instance of User
        $pedagogicUser->roles()->attach($role);
        $category = Category::create(['name' => 'Category 1','role_id' => $role->id]);
        $document = Document::create([
            'name' => 'Document 1',
            'content' => 'Content 1',
            'excerpt' => 'Excerpt 1',
            'created_by' => $user->id,
        ]);
        $document->categories()->attach($category->id);
        $document = Document::with('categories.role')->find($document->id);

        $this->assertNotNull($document->categories->first()->role);

        $adminUser = User::factory()->create();
        $adminUser = User::find($adminUser->id); // Ensure $adminUser is an instance of User
        $adminRole = Role::create(['name'=> "Admin $role->name"]);
        $adminUser->roles()->attach($adminRole);
        $this->actingAs($pedagogicUser);
        $response = $this->delete(route('documents.destroy', $document));
        $response->assertStatus(403);
        $this->assertDatabaseHas('documents', ['name' => 'Document 1']);
        $this->assertNotNull(Document::find($document->id));
        $this->actingAs($adminUser);
        $response = $this->delete(route('documents.destroy', $document));
        $response->assertRedirect(route('documents.index'));
        $this->assertSoftDeleted('documents', ['name' => 'Document 1']);
        $this->assertNull(Document::find($document->id));
        $this->assertNotNull(Document::withTrashed()->find($document->id));
    }
}
