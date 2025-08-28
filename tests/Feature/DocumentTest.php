<?php

use App\Models\Category;
use App\Models\Document;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);
uses(\Illuminate\Foundation\Testing\WithFaker::class);

test('index displays categories and documents', function (): void {
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
        'formated_name' => 'test_name'
    ]);
    $category->documents()->attach($document->id);

    // Act
    $this->actingAs($user);
    $response = $this->get(route('documents.index'));

    // Assert
    $response->assertStatus(200);
    $response->assertViewHas('categories', function ($categories) use ($category) {
        return $categories->contains($category);
    });
});

test('create displays form with roles', function (): void {
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
});

test('store creates new document', function (): void {
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
});

test('show displays document when user is author', function (): void {
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
        'formated_name' => 'test_name'
    ]);
    $document->categories()->attach($category->id);

    // Act
    $response = $this->get(route('documents.show', $document->id));

    // Assert
    $response->assertStatus(200);
    $response->assertViewHas('document', $document);
});

test('user without role is redirected when accessing document', function (): void {
    $user = User::factory()->create();
    $author = User::factory()->create();
    $role = Role::create(['name'=> 'pédagogie']);
    $author->roles()->attach($role);

    $document = Document::create([
        'name' => 'Document 1',
        'content' => 'Content 1',
        'excerpt' => 'Excerpt 1',
        'created_by' => $author->id,
        'formated_name' => 'test_name'
    ]);

    $this->actingAs($user);
    $response = $this->get(route('documents.show', $document->id));
    $response->assertStatus(403);
});

test('show displays document when user got proper role', function (): void {
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
        'formated_name' => 'test_name'
    ]);
    $document->categories()->attach($category->id);

    // Act
    $this->actingAs($user);
    $response = $this->get(route('documents.show', $document->id));

    // Assert
    $response->assertStatus(200);
    $response->assertViewHas('document', $document);
});
test('document can be see with a permission', function (): void {
    //arrange
    $user = User::factory()->create();
    $user = User::find($user->id);
    $author = User::factory()->create();
    $role = Role::create(['name'=> 'pédagogie']);
    $author->roles()->attach($role);
    $document = Document::create([
        'name' => 'Document 1',
        'content' => 'Content 1',
        'excerpt' => 'Excerpt 1',
        'created_by' => $author->id,
        'formated_name' => 'test_name'
    ]);
    //acting as unauthorized user
    $unauthorizedUser = User::factory()->create();
    $unauthorizedUser = User::find($unauthorizedUser->id);
    $this->actingAs($unauthorizedUser);
    $response = $this->get(route('documents.show', $document->id));
    $response->assertStatus(403);
    //arrange and acting as authorized user
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
});

test('document can be updated by superadmin', function (): void {
    $user = User::factory()->create();
    $user = User::find($user->id);
    // Ensure $user is an instance of User
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
});
test('document can be soft deleted by superadmin', function (): void {
    $user = User::factory()->create();
    $user = User::find($user->id);
    // Ensure $user is an instance of User
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
});
test('admin roles can update document', function (): void {
    $user = User::factory()->create();
    $user = User::find($user->id);
    // Ensure $user is an instance of User
    $role = Role::create(['name'=> 'pédagogie']);
    $user->roles()->attach($role);
    $pedagogicUser = User::factory()->create();
    $pedagogicUser = User::find($pedagogicUser->id);
    // Ensure $pedagogicUser is an instance of User
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

    expect($document->categories->first()->role)->not->toBeNull();

    $adminUser = User::factory()->create();
    $adminUser = User::find($adminUser->id);
    // Ensure $adminUser is an instance of User
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
});

test('admin roles can delete document', function (): void {
    $user = User::factory()->create();
    $user = User::find($user->id);
    // Ensure $user is an instance of User
    $role = Role::create(['name'=> 'pédagogie']);
    $user->roles()->attach($role);
    $pedagogicUser = User::factory()->create();
    $pedagogicUser = User::find($pedagogicUser->id);
    // Ensure $pedagogicUser is an instance of User
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

    expect($document->categories->first()->role)->not->toBeNull();

    $adminUser = User::factory()->create();
    $adminUser = User::find($adminUser->id);
    // Ensure $adminUser is an instance of User
    $adminRole = Role::create(['name'=> "Admin $role->name"]);
    $adminUser->roles()->attach($adminRole);
    $this->actingAs($pedagogicUser);
    $response = $this->delete(route('documents.destroy', $document));
    $response->assertStatus(403);
    $this->assertDatabaseHas('documents', ['name' => 'Document 1']);
    expect(Document::find($document->id))->not->toBeNull();
    $this->actingAs($adminUser);
    $response = $this->delete(route('documents.destroy', $document));
    $response->assertRedirect(route('documents.index'));
    $this->assertSoftDeleted('documents', ['name' => 'Document 1']);
    expect(Document::find($document->id))->toBeNull();
    expect(Document::withTrashed()->find($document->id))->not->toBeNull();
});
