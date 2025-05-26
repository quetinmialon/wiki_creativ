<?php

use App\Models\Document;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('displays permissions list', function (): void {
    $user = User::factory()->create();
    $user->roles()->attach(Role::factory()->create(['name' => 'superadmin']));
    $user = User::find($user->id);

    // Ensure $user is an instance of User
    $this->actingAs($user)
        ->get(route('admin.permissions'))
        ->assertStatus(200)
        ->assertViewIs('permission.permission-list');
});

test('displays pending permissions', function (): void {
    $user = User::factory()->create();
    $user->roles()->attach(Role::factory()->create(['name' => 'superadmin']));
    $user = User::find($user->id);

    // Ensure $user is an instance of User
    $document = Document::factory()->create(['created_by' => $user->id]);

    for ($i = 1; $i <= 6; $i++) {
        Permission::create([
            'document_id' => $document->id,
            'expired_at' => now()->addDays(7),
            'comment' => 'Test comment',
            'author' => $user->id,
            'status' => 'pending'
        ]);
    }
    $this->actingAs($user);
    $this->get(route('admin.permissions.pendings'))
        ->assertStatus(200)
        ->assertViewIs('permission.pending-permission-list');
});

test('creates a new permission request', function (): void {
    $user = User::factory()->create();
    $user = User::find($user->id);
    // Ensure $user is an instance of User
    $document = Document::factory()->create(['created_by' => $user->id]);

    $this->actingAs($user)
        ->post(route('permissions.create'), [
            'document_id' => $document->id,
            'expired_at' => now()->addDays(7),
            'comment' => 'Test comment',
        ])
        ->assertRedirect(route('documents.allDocumentsInfo'))
        ->assertSessionHas('success', 'Demande de permission créée avec succès.');

    $this->assertDatabaseHas('permissions', [
        'document_id' => $document->id,
        'status' => 'pending',
    ]);
});

test('handles a permission request', function (): void {
    $user = User::factory()->create();
    $user->roles()->attach(Role::factory()->create(['name' => 'superadmin']));
    $user = User::find($user->id);
    // Ensure $user is an instance of User
    $document = Document::factory()->create(['created_by' => $user->id]);
    $permission = Permission::create([
        'document_id' => $document->id,
        'expired_at' => now()->addDays(7),
        'comment' => 'Test comment',
        'author' => $user->id,
        'status' => 'pending'
    ]);

    $this->actingAs($user)
        ->post(route('admin.permissions.handle', $permission->id), ['status' => 'approved'])
        ->assertRedirect(route('admin.permissions.pendings'))
        ->assertSessionHas('success', 'Demande de permission modifiée avec succès.');

    $this->assertDatabaseHas('permissions', [
        'id' => $permission->id,
        'status' => 'approved',
    ]);
});

test('deletes a permission request', function (): void {
    $user = User::factory()->create();
    $user->roles()->attach(Role::factory()->create(['name' => 'superadmin']));
    $user = User::find($user->id);
    // Ensure $user is an instance of User
    $document = Document::factory()->create(['created_by' => $user->id]);
    $this->actingAs($user)
        ->post(route('permissions.create'), [
            'document_id' => $document->id,
            'expired_at' => now()->addDays(7),
            'comment' => 'Test comment',
        ])
        ->assertRedirect(route('documents.allDocumentsInfo'))
        ->assertSessionHas('success', 'Demande de permission créée avec succès.');
    $this->assertDatabaseHas('permissions', [
        'document_id' => $document->id,
        'status' => 'pending',
    ]);
    $permission = Permission::create([
        'document_id' => $document->id,
        'expired_at' => now()->addDays(7),
        'comment' => 'Test comment',
        'author' => $user->id,
        'status' => 'pending'
    ]);

    $this->actingAs($user)
        ->delete(route('permissions.destroy', $permission->id))
        ->assertRedirect(route('admin.permissions.pendings'))
        ->assertSessionHas('success', 'Demande de permission supprimée avec succès.');

    $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
});

