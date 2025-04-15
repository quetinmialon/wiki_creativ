<?php

namespace Tests\Unit\Models;

use App\Models\Credential;
use App\Models\Document;
use App\Models\Favorite;
use App\Models\Log;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_user()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $this->assertEquals('Test User', $user->name);
    }

    public function test_user_has_roles_relationship()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();

        $user->roles()->attach($role);

        $this->assertTrue($user->roles->contains($role));
    }

    public function test_user_has_permissions_relationship()
    {
        $user = User::factory()->create();
        $permission = Permission::factory()->create(['author' => $user->id,'expired_at'=> now()->addDays(7)]);

        $this->assertTrue($user->permissions->contains($permission));
    }

    public function test_user_has_favorites_relationship()
    {
        $user = User::factory()->create();
        $favorite = Favorite::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->favorites->contains($favorite));
    }

    public function test_user_has_credentials_relationship()
    {
        $user = User::factory()->create();
        $credential = Credential::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->credentials->contains($credential));
    }

    public function test_user_has_logs_relationship()
    {
        $user = User::factory()->create();
        $log = Log::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->logs->contains($log));
    }

    public function test_user_has_wrote_documents_relationship()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create(['created_by' => $user->id]);

        $this->assertTrue($user->wrote->contains($document));
    }

    public function test_user_has_updated_documents_relationship()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create(['updated_by' => $user->id]);

        $this->assertTrue($user->updated_documents->contains($document));
    }

    public function test_user_can_be_soft_deleted()
    {
        $user = User::factory()->create();
        $user->delete();

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_user_load_roles_method()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();
        $user->roles()->attach($role);

        $loadedUser = $user->loadRoles();

        $this->assertTrue($loadedUser->relationLoaded('roles'));
        $this->assertTrue($loadedUser->roles->contains($role));
    }
}
