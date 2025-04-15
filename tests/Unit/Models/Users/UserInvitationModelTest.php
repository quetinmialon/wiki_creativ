<?php

namespace Tests\Unit\Models\Users;

use App\Models\Role;
use App\Models\User\UserInvitation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserInvitationModelTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_can_create_a_user_invitation()
    {
        $invitation = UserInvitation::factory()->create([
            'email' => 'invited@example.com',
            'token' => 'test-token-123'
        ]);

        $this->assertDatabaseHas('user_invitations', [
            'email' => 'invited@example.com',
            'token' => 'test-token-123',
        ]);
    }

    public function test_it_can_update_a_user_invitation()
    {
        $invitation = UserInvitation::factory()->create([
            'email' => 'invited@example.com',
            'token' => 'initial-token',
        ]);

        $invitation->update([
            'email' => 'updated@example.com',
            'token' => 'updated-token',
        ]);

        $this->assertDatabaseHas('user_invitations', [
            'id' => $invitation->id,
            'email' => 'updated@example.com',
            'token' => 'updated-token',
        ]);
    }

    public function test_it_can_delete_a_user_invitation()
    {
        $invitation = UserInvitation::factory()->create();

        $invitation->delete();

        $this->assertDatabaseMissing('user_invitations', [
            'id' => $invitation->id,
        ]);
    }

    public function test_user_invitation_has_roles_relationship()
    {
        $invitation = UserInvitation::factory()->create();
        $role = Role::factory()->create();

        $invitation->roles()->attach($role);

        $this->assertTrue($invitation->roles->contains($role));
    }

    public function test_user_invitation_can_sync_roles()
    {
        $invitation = UserInvitation::factory()->create();
        $roles = Role::factory()->count(3)->create();

        $invitation->roles()->sync([$roles[0]->id, $roles[1]->id]);

        $this->assertCount(2, $invitation->roles);
        $this->assertTrue($invitation->roles->contains($roles[0]));
        $this->assertTrue($invitation->roles->contains($roles[1]));
        $this->assertFalse($invitation->roles->contains($roles[2]));
    }

    public function test_user_invitation_can_detach_roles()
    {
        $invitation = UserInvitation::factory()->create();
        $roles = Role::factory()->count(2)->create();

        $invitation->roles()->attach($roles);
        $invitation->roles()->detach($roles[0]);

        $invitation->refresh();

        $this->assertFalse($invitation->roles->contains($roles[0]));
        $this->assertTrue($invitation->roles->contains($roles[1]));
    }
}
