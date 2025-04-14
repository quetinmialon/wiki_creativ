<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use DatabaseTransactions;


    public function test_displays_permissions_list()
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::factory()->create(['name' => 'superadmin']));
        $user = User::find($user->id); // Ensure $user is an instance of User

        $this->actingAs($user)
            ->get(route('admin.permissions'))
            ->assertStatus(200)
            ->assertViewIs('permission.permission-list');
    }

    public function test_displays_pending_permissions()
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::factory()->create(['name' => 'superadmin']));
        $user = User::find($user->id); // Ensure $user is an instance of User

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
    }

    public function test_creates_a_new_permission_request()
    {
        $user = User::factory()->create();
        $user = User::find($user->id); // Ensure $user is an instance of User
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
    }

    public function test_handles_a_permission_request()
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::factory()->create(['name' => 'superadmin']));
        $user = User::find($user->id); // Ensure $user is an instance of User
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
    }

    public function test_deletes_a_permission_request()
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::factory()->create(['name' => 'superadmin']));
        $user = User::find($user->id); // Ensure $user is an instance of User
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
    }

    public function test_userlist_permissions_only_displays_user_permissions()
    {

        $user = User::factory()->create();
        $user->roles()->attach(Role::factory()->create(['name' => 'superadmin']));
        $user = User::find($user->id); // Ensure $user is an instance of User
        $document = Document::factory()->create(['created_by' => $user->id]);
        $user2 = User::factory()->create();



        Permission::create([
            'document_id' => $document->id,
            'expired_at' => now()->addDays(7),
            'comment' => 'Test comment',
            'author' => $user2->id,
            'status' => 'pending'
        ]);

        $this->actingAs($user)
            ->get(route('permissions.user', $user2->id))
            ->assertStatus(200)
            ->assertViewIs('permission.user-request-list')
            ->assertViewHas('permissions', function ($permissions) use ($user2) {
                return $permissions->contains('author', $user2->id);
            });

        Permission::create([
            'document_id' => $document->id,
            'expired_at' => now()->addDays(7),
            'comment' => 'Test comment',
            'author' => $user->id,
            'status' => 'pending'
        ]);
        $this->actingAs($user)
        ->get(route('permissions.user', $user->id))
        ->assertStatus(200)
        ->assertViewIs('permission.user-request-list')
        ->assertViewHas('permissions', function ($permissions) use ($user2) {
            return !$permissions->contains('author', $user2->id);
        })
        ->assertViewHas('permissions', function ($permissions) use ($user) {
            return $permissions->contains('author', $user->id);
        });
    }
}
