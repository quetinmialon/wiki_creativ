<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $user2;

    protected $document;
    protected $document2;

    protected function setUp(): void
    {
        $this->user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);
        $this->user2 = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'password' => bcrypt('password123'),
        ]);
        $this->document = Document::create(
            [
                'name' => 'Document 1',
                'content' => 'Content 1',
                'excerpt' => 'Excerpt 1',
                'created_by' => $this->user->id,
            ]
        );
        $this->document2 = Document::create(
            [
                'name' => 'Document 2',
                'content' => 'Content 2',
                'excerpt' => 'Excerpt 2',
                'created_by' => $this->user2->id,
            ]
        );

    }

    /** @test */
    public function test_displays_permissions_list()
    {
        $this->actingAs($this->user)
            ->get(route('permissions.index'))
            ->assertStatus(200)
            ->assertViewIs('permission.permission-list');
    }

    /** @test */
    public function test_displays_pending_permissions()
    {
        $this->actingAs($this->user);

        for ($i = 1; $i <= 6; $i++) {
            Permission::create([
                'document_id' => $this->document->id,
                'expired_at' => now()->addDays(7),
                'comment' => 'Test comment',
                'author' => $this->user->id,
                'status' => 'pending'
            ]);
        }

        $this->get(route('pending-permissions'))
            ->assertStatus(200)
            ->assertViewIs('permission.pending-permission-list');
    }

    /** @test */
    public function test_creates_a_new_permission_request()
    {

        $this->actingAs($this->user)
            ->post(route('permissions.create'), [
                'document_id' => $this->document->id,
                'expired_at' => now()->addDays(7),
                'comment' => 'Test comment',
            ])
            ->assertRedirect(route('pending-permissions'))
            ->assertSessionHas('success', 'Demande de permission créée avec succès.');

        $this->assertDatabaseHas('permissions', [
            'document_id' => $this->document->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function test_handles_a_permission_request()
    {
        $permission = Permission::create([
            'document_id' => $this->document->id,
            'expired_at' => now()->addDays(7),
            'comment' => 'Test comment',
            'author' => $this->user->id,
            'status' => 'pending'
        ]);
        $this->actingAs($this->user)
            ->post(route('permissions.handle', $permission->id), ['status' => 'approved'])
            ->assertRedirect(route('pending-permissions'))
            ->assertSessionHas('success', 'Demande de permission modifiée avec succès.');

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'status' => 'approved',
        ]);
    }

    /** @test */
    public function test_deletes_a_permission_request()
    {
        $permission = Permission::create([
            'document_id' => $this->document->id,
            'expired_at' => now()->addDays(7),
            'comment' => 'Test comment',
            'author' => $this->user->id,
            'status' => 'pending'
        ]);

        $this->actingAs($this->user)
            ->delete(route('permissions.destroy', $permission->id))
            ->assertRedirect(route('pending-permissions'))
            ->assertSessionHas('success', 'Demande de permission supprimée avec succès.');

        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }

    /** @test */
    public function test_cancels_a_pending_request()
    {
        $permission = Permission::create([
            'document_id' => $this->document->id,
            'expired_at' => now()->addDays(7),
            'comment' => 'Test comment',
            'author' => $this->user->id,
            'status' => 'pending'
        ]);

        $this->actingAs($this->user)
            ->delete(route('permissions.cancel', $permission->id))
            ->assertRedirect(route('pending-permissions'))
            ->assertSessionHas('success', 'Demande de permission annulée avec succès.');

        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }

    public function test_userlist_permissions_only_displays_user_permissions()
    {
        Permission::create([
            'document_id' => $this->document->id,
            'expired_at' => now()->addDays(7),
            'comment' => 'Test comment',
            'author' => $this->user2->id,
            'status' => 'pending'
        ]);

        $this->actingAs($this->user)
            ->get(route('permissions.user', $this->user2->id))
            ->assertStatus(200)
            ->assertViewIs('permission.user-request-list')
            ->assertViewHas('permissions', function ($permissions) {
                return $permissions->contains('author', $this->user2->id);
            });
        $this->actingAs($this->user2)
        ->get(route('permissions.user', $this->user->id))
        ->assertStatus(200)
        ->assertViewIs('permission.user-request-list');
    }
}
