<?php

namespace Tests\Feature;

use App\Models\Credential;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class CredentialTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test store method for creating a credential.
     */
    public function test_store_credential()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);
        $role = Role::create([
            'name'=> 'pédagogie',
        ]);

        $this->actingAs($user)
            ->post(route('credentials.store'), [
                'destination' => 'example.com',
                'username' => 'testuser',
                'password' => 'password123',
                'role_id' => $role->id,
            ])
            ->assertRedirect()
            ->assertSessionHas('success', 'logs créés avec succès');

        $this->assertDatabaseHas('credentials', [
            'destination' => 'example.com',
            'username' => 'testuser',
            'user_id' => $user->id,
        ]);

        $credential = Credential::where('destination','=','example.com')->first();
        $this->assertEquals('password123', Crypt::decryptString($credential->password));
    }

    /**
     * Test store method when user is not authenticated.
     */
    public function test_store_requires_authentication()
    {
        $this->post(route('credentials.store'), [
            'destination' => 'example.com',
            'username' => 'testuser',
            'password' => 'password123',
        ])
            ->assertRedirect();
    }

    /**
     * Test index method for displaying user credentials.
     */
    public function test_index_shows_user_credentials_and_shared_ones()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);
        $role = Role::create([
            'name'=> 'qualité',
        ]);
        $user->roles()->attach($role->id);

        $personalCredential = Credential::create([
            'destination' => 'example.com',
            'username' => 'testuser',
            'password' => Crypt::encryptString('password123'),
            'role_id' => null, // personal credential
            'user_id' => $user->id,
        ]);

        $sharedCredential = Credential::create([
            'destination' => 'anotherexample.com',
            'username' => 'anothertestuser',
            'password' => Crypt::encryptString('password123'),
            'user_id' => $user->id,
            'role_id' => $role->id, //shared credentials
        ]);

        $this->actingAs($user)
            ->get(route('credentials.index'))
            ->assertOk()
            ->assertSee($personalCredential->destination)
            ->assertSee($sharedCredential->destination);
    }

    /**
     * Test edit method for showing the credential edit form.
     */
    public function test_edit_shows_edit_form()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);
        $credential = Credential::create([
            'destination' => 'example.com',
            'username' => 'testuser',
            'role_id' => null, // personal credential
            'user_id' => $user->id,
            'password' => Crypt::encryptString('password123'),
        ]);

        $this->actingAs($user)
            ->get(route('credentials.edit', $credential->id))
            ->assertOk()
            ->assertSee('password123') // Decrypted password
            ->assertSee($credential->destination);
    }

    /**
     * Test edit method when user is not authorized.
     */
    public function test_edit_prevents_unauthorized_access()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);
        $otherUser = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'password' => bcrypt('password456'),
        ]);
        $credential = Credential::create([
            'destination' => 'example.com',
            'username' => 'testuser',
            'role_id' => null, // personal credential
            'password' => Crypt::encryptString('password123'),
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($user)
            ->get(route('credentials.edit', $credential->id))
            ->assertRedirect()
            ->assertSessionHas('error', 'vous ne pouvez pas modifier ce log');
    }

    /**
     * Test update method for updating a credential.
     */
    public function test_update_credential()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);
        $credential = Credential::create([
            'destination' => 'example.com',
            'username' => 'testuser',
            'role_id' => null, // personal credential
            'user_id' => $user->id,
            'password' => Crypt::encryptString('password123'),
        ]);

        $this->actingAs($user)
            ->put(route('credentials.update', $credential->id), [
                'destination' => 'new-example.com',
                'username' => 'updateduser',
                'password' => 'newpassword123',
            ])
            ->assertRedirect()
            ->assertSessionHas('success', 'logs modifiés avec succès');

        $credential->refresh();
        $this->assertEquals('new-example.com', $credential->destination);
        $this->assertEquals('updateduser', $credential->username);
        $this->assertEquals('newpassword123', Crypt::decryptString($credential->password));
    }

    /**
     * Test destroy method for deleting a credential.
     */
    public function test_destroy_credential()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);
        $credential = Credential::create([
            'destination' => 'example.com',
            'username' => 'testuser',
            'password' => Crypt::encryptString('password123'),
            'user_id' => $user->id,
            'role_id' => null, // personal credential
        ]);

        $this->actingAs($user)
            ->delete(route('credentials.destroy', $credential->id))
            ->assertRedirect()
            ->assertSessionHas('success', 'logs supprimés avec succès');

        $this->assertDatabaseMissing('credentials', ['id' => $credential->id]);
    }

    /**
     * Test destroy method when user is not authorized.
     */
    public function test_destroy_prevents_unauthorized_access()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);

        $otherUser = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'password' => bcrypt('password456'),
        ]);
        $credential = Credential::create([
            'destination' => 'example.com',
            'username' => 'testuser',
            'password' => Crypt::encryptString('password123'),
            'role_id' => null, // personal credential
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($user)
            ->delete(route('credentials.destroy', $credential->id))
            ->assertRedirect()
            ->assertSessionHas('error', 'vous ne pouvez pas supprimer ce log');

        $this->assertDatabaseHas('credentials', ['id' => $credential->id]);
    }


    public function test_personnal_credentials_arent_display_to_others_users()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $role = Role::factory()->create(['name' => 'pédagogie']);
        $user->roles()->attach($role->id);
        $otherUser->roles()->attach($role->id);
        $otherUser = User::find($otherUser->id); // Ensure $otherUser is an instance of User
        $credential = Credential::factory()->create([
            'role_id' => null, // personal credential
            'user_id' => $user->id,
        ]);
        $sharedCredential = Credential::factory()->create([
            'user_id' => $otherUser->id,
            'role_id' => $role->id, //shared credentials
        ]);
        $this->actingAs($otherUser)
            ->get(route('credentials.index'))
            ->assertOk()
            ->assertSee($sharedCredential->destination)
            ->assertDontSee($credential->destination);
    }
    public function test_shared_credentials_are_displayed_to_users_with_the_proper_role()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $thirdUser = User::factory()->create();

        $role = Role::factory()->create(['name' => 'pédagogie']);
        $user->roles()->attach($role->id);
        $otherUser->roles()->attach($role->id);
        $user = User::find($user->id); // Ensure $user is an instance of User
        $otherUser = User::find($otherUser->id); // Ensure $otherUser is an instance of User
        $thirdUser = User::find($thirdUser->id); // Ensure $thirdUser is an instance of User
        $credential = Credential::factory()->create([
            'role_id' => null, // personal credential
            'user_id' => $user->id,
        ]);
        $sharedCredential = Credential::factory()->create([
            'user_id' => $otherUser->id,
            'role_id' => $role->id, //shared credentials
        ]);
        $this->actingAs($user)
            ->get(route('credentials.index'))
            ->assertOk()
            ->assertSee($sharedCredential->destination)
            ->assertSee($credential->destination);
        $this->actingAs($otherUser)
            ->get(route('credentials.index'))
            ->assertOk()
            ->assertSee($sharedCredential->destination)
            ->assertDontSee($credential->destination);
        $this->actingAs($thirdUser)
            ->get(route('credentials.index'))
            ->assertOk()
            ->assertDontSee($sharedCredential->destination)
            ->assertDontSee($credential->destination);
        $this->assertDatabaseHas('credentials', [
            'destination' => $sharedCredential->destination,
            'username' => $sharedCredential->username,
            'user_id' => $otherUser->id,
        ]);
    }
}
