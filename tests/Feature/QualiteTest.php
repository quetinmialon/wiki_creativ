<?php

use App\Models\Document;
use App\Models\User;
use App\Models\Role;

test('Document cant be seen by user if is not confirmed by qualite members', function () {
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

test('document can be seen if qualité members validate it',function (){
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

test('qualite member can set formated_name on document', function(){
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

test('basic members cant add formated_name to document via qualite pages', function(){
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

test('qualite members can remove formated_name from document', function () {
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

test('basic members cant remove formated_name from document', function(){
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

test('qualite members can access not formated document list', function (){
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

test('basic members cant access not formated document list', function(){
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
