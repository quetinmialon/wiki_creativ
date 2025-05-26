<?php

use App\Models\Document;
use App\Models\User;
use App\Models\Role;
use App\Models\Category;

test('Document cant be seen by user if is not confirmed by qualite members', function (): void {
    //arrange
    $user = User::factory()->create();
    $role = Role::factory() ->create([
        "name"=> "pedagogie"
    ]);
    $user->roles()->attach($role);
    $document = Document::factory()->create([
        'created_by' => $user->id,
        'formated_name' => null //this means hasn't been validate by qualite members
    ]);

    //act
    $response = $this->actingAs($user)
        ->get(route('documents.show', $document->id));

    //assert
    $response->assertStatus(302);
    $response->assertRedirect('documents');
});

test('document can be seen if qualité members validate it',function (): void{
    //arrange
    $user = User::factory()->create();
    $user = User::find($user->id); // Ensure $user is an instance of User
    $role = Role::factory() ->create([
        "name"=> "pedagogie"
    ]);
    $user->roles()->attach($role);
    $document = Document::factory()->create([
        'formated_name' => 'formated_name 1',//this means has been validate by qualite members and has a formated name
        'created_by'=> $user->id
    ]);

    //act
    $response = $this->actingAs($user)
        ->get(route('documents.show', $document->id));

    //assert
    $response->assertStatus(200);
    $response->assertViewHas('document', $document);
});

test('qualite member can set formated_name on document', function(): void{
    //arrange
    $user = User::factory()->create();
    $role = Role::factory()->create([
        "name" => "qualité"
    ]);
    $user->roles()->attach($role);
    $document = Document::factory()->create([
        'name' => 'Document 1',
        'content' => 'Content 1',
        'excerpt' => 'Excerpt 1',
        'formated_name' => null,
        'created_by' => $user->id
    ]);

    //act
    $response = $this->actingAs($user)
        ->put(route('qualite.addNormedName', $document->id), [
            'formated_name' => 'Updated Formated Name',
            'id' => $document->id
        ]);

    //assert
    $response->assertStatus(302);
    $response->assertSessionHas('success', 'Nomanclature ajouté avec succès, le document est accessible aux utilisateurs.');
    $this->assertDatabaseHas('documents', [
        'id' => $document->id,
        'formated_name' => 'Updated Formated Name'
    ]);
});

test('basic members cant add formated_name to document via qualite pages', function(): void{
    //arrange
    $user = User::factory()->create();
    $role = Role::factory()->create(
        ['name' => 'pédagogie']
    );
    $user->roles()->attach($role);
    $document = Document::factory()->create([
        'name' => 'Document 1',
        'content' => 'Content 1',
        'excerpt' => 'Excerpt 1',
        'formated_name' => null,
        'created_by' => $user->id
    ]);

    //act
    $response = $this->actingAs($user)
        ->put(route('qualite.addNormedName', $document->id), [
            'id' => $document->id,
            'formated_name' => 'Unauthorized Update'
        ]);

    //assert
    $response->assertStatus(302);
    $response->assertSessionHas('error');
    $this->assertDatabaseMissing('documents', [
        'id' => $document->id,
        'formated_name' => 'Unauthorized Update'
    ]);
});

test('qualite members can remove formated_name from document', function (): void {
    //arrange
    $user = User::factory()->create();
    $role = Role::factory()->create([
        "name" => "qualité"
    ]);
    $user->roles()->attach($role);
    $document = Document::factory()->create([
        'name' => 'Document 1',
        'content' => 'Content 1',
        'excerpt' => 'Excerpt 1',
        'formated_name' => 'test_formated_name',
        'created_by' => $user->id
    ]);

    //act
    $response = $this->actingAs($user)
        ->put(route('qualite.addNormedName', $document->id), [
            'formated_name' => null,
            'id' => $document->id
        ]);

    //assert
    $response->assertStatus(302);
    $response->assertSessionHas('success', "nomenclature retirée avec succès, le document n'est plus accessible aux utilisateurs");
    $this->assertDatabaseMissing('documents', [
        'id' => $document->id,
        'formated_name' => 'test_formated_name'
    ]);
});

test('basic members cant remove formated_name from document', function(): void{
    //arrange
    $user = User::factory()->create();
    $role = Role::factory()->create(
        ['name' => 'pédagogie']
    );
    $user->roles()->attach($role);
    $document = Document::factory()->create([
        'name' => 'Document 1',
        'content' => 'Content 1',
        'excerpt' => 'Excerpt 1',
        'formated_name' => 'Unauthorized Update',
        'created_by' => $user->id
    ]);

    //act
    $response = $this->actingAs($user)
        ->put(route('qualite.addNormedName', $document->id), [
            'id' => $document->id,
            'formated_name' => null
        ]);

    //assert
    $response->assertStatus(302);
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('documents', [
        'id' => $document->id,
        'formated_name' => 'Unauthorized Update'
    ]);
});

test('qualite members can access not formated document list', function (): void{
    $user = User::factory()->create();
    $role = Role::factory()->create([
        "name" => "qualité"
    ]);
    $user->roles()->attach($role);

    $response = $this->actingAs($user)
        ->get(route('qualite.index'));

    $response->assertStatus(200);
    $response->assertViewIs('qualite.index');
    $response->assertViewHas('document');
});

test('basic members cant access not formated document list', function(): void{
    $user = User::factory()->create();
    $role = Role::factory()->create([
        "name" => "pédagogie"
    ]);
    $user->roles()->attach($role);

    $response = $this->actingAs($user)
        ->get(route('qualite.index'));

    $response->assertStatus(302);
    $response->assertRedirect(route('home'));
    $response->assertSessionHas('error');
});
test('redirects guests to login on index', function (): void {
    $this->get(route('qualite.index'))
         ->assertRedirect(route('login'))
         ->assertSessionHas('error', 'Connectez vous pour acceder à cette page');
});
test('edit returns the edit view with document and roles', function (): void {
    $user = User::factory()->create();
    $role = Role::where('name','qualité')->firstOrFail();
    $user->roles()->attach($role);
    $this->actingAs($user);

    $document = Document::factory()->create();
    $category = Category::factory()->create(['role_id' => $role->id]);
    $document->categories()->attach($category->id);

    $this->get(route('qualite.edit', ['id' => $document->id]))
        ->assertOk()
        ->assertViewIs('qualite.edit_document')
        ->assertViewHasAll(['document', 'roles']);
});

test('edit redirects if document does not exist', function (): void {
    $user = User::factory()->create();
    $role = Role::where('name','qualité')->firstOrFail();
    $user->roles()->attach($role);
    $this->actingAs($user);

    $this->get(route('qualite.edit', ['id' => 999]))
        ->assertRedirect(route('qualite.index'))
        ->assertSessionHas('error', 'Le document n\'existe pas en base de donnée');
});

test('update fails validation with missing required fields', function (): void {
    $user = User::factory()->create();
    $role = Role::where('name','qualité')->firstOrFail();
    $user->roles()->attach($role);
    $this->actingAs($user);

    $this->put(route('qualite.update', ['id' => 1]), [])
        ->assertSessionHasErrors(['name', 'id', 'content']);
});

test('update fails if categories_id is empty', function (): void {
    $user = User::factory()->create();
    $role = Role::where('name','qualité')->firstOrFail();
    $user->roles()->attach($role);
    $this->actingAs($user);

    $document = Document::factory()->create();

    $this->put(route('qualite.update', ['id' => $document->id]), [
        'name' => 'Test',
        'id' => $document->id,
        'content' => '<p>Contenu valide</p>',
        'categories_id' => [],
    ])
        ->assertRedirect()
        ->assertSessionHasErrors('categories_id');
});

test('update redirects if document does not exist', function (): void {
    $user = User::factory()->create();
    $role = Role::where('name','qualité')->firstOrFail();
    $user->roles()->attach($role);
    $this->actingAs($user);

    $this->put(route('qualite.update', ['id' => 999]), [
        'name' => 'Test',
        'id' => 999,
        'content' => '<p>Contenu valide</p>',
        'categories_id' => [1],
    ])
        ->assertRedirect(route('qualite.index'))
        ->assertSessionHas('error', 'Le document n\'existe pas en base de donnée');
});

test('update succeeds with valid data', function (): void {
    $user = User::factory()->create();
    $role = Role::where('name','qualité')->firstOrFail();
    $user->roles()->attach($role);
    $this->actingAs($user);

    $document = Document::factory()->create();
    $category = Category::factory()->create(['role_id' => $role->id]);

    $this->put(route('qualite.update', ['id' => $document->id]), [
        'name' => 'Document modifié',
        'formated_name' => 'DOC-001',
        'id' => $document->id,
        'content' => '<p>Contenu valide</p>',
        'excerpt' => 'Résumé',
        'categories_id' => [$category->id],
    ])
        ->assertRedirect(route('qualite.index'))
        ->assertSessionHas('success', 'Document mis à jour avec succès.');

    $this->assertDatabaseHas('documents', [
        'id' => $document->id,
        'name' => 'Document modifié',
        'formated_name' => 'DOC-001',
    ]);
});

test('documentList view returns list of normed documents', function (): void {
    $user = User::factory()->create();
    $role = Role::where('name','qualité')->firstOrFail();
    $user->roles()->attach($role);
    $this->actingAs($user);

    Document::factory()->create(['formated_name' => 'DOC-001']);

    $this->get(route('qualite.documents'))
        ->assertOk()
        ->assertViewIs('qualite.document_list')
        ->assertViewHas('document');
});

test('qualité pages redirect if not auth', function(): void
{
    $this->get(route('qualite.index'))
        ->assertRedirect(route('login'));
});
