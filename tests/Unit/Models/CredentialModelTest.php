<?php

namespace Tests\Unit\Models;

use App\Models\Role;
use App\Models\Document;
use App\Models\Credential;
use App\Models\User;
use Crypt;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Crypt as FacadesCrypt;
use Tests\TestCase;

class CredentialModelTest extends TestCase
{
    use DatabaseTransactions;

    public function test_credential_can_be_created()
    {
        $user = User::factory()->create();
        $Credential = Credential::factory()->create([
            'user_id' => $user->id,
            'destination'=> 'Test Destination',
            'password'=> 'Test Password',
            'username'=> 'Test Username'
        ]);

        $this->assertDatabaseHas('Credentials', [
            'user_id' => $user->id,
            'destination'=> 'Test Destination',
            'password'=> 'Test Password',
            'username'=> 'Test Username'
        ]);

        $this->assertInstanceOf(Credential::class, $Credential);
    }

    public function test_credential_can_be_deleted()
    {
        $user = User::factory()->create();
        $Credential = Credential::factory()->create([
            'user_id' => $user->id,
            'destination'=> 'Test Destination',
            'password'=> 'Test Password',
            'username'=> 'Test Username'
        ]);
        $this->assertDatabaseHas('Credentials', [
            'id' => $Credential->id,
        ]);
        $Credential->delete();

        $this->assertDatabaseMissing('Credentials', ['id' => $Credential->id]);
    }
    public function test_credential_can_be_updated()
    {
        $user = User::factory()->create();
        $Credential = Credential::factory()->create([
            'user_id' => $user->id,
            'destination'=> 'Test Destination',
            'password'=> 'Test Password',
            'username'=> 'Test Username'
        ]);

        $Credential->update(
            [
                'destination'=> 'Updated Destination',
                'password'=> 'Updated Password',
                'username'=> 'Updated Username'
            ]
        );

        $this->assertDatabaseHas('Credentials', [
            'destination'=> 'Updated Destination',
            'password'=> 'Updated Password',
            'username'=> 'Updated Username'
        ]);
    }
    public function test_credential_belongs_to_user()
    {
        $user = User::factory()->create();
        $Credential = Credential::factory()->create([
            'user_id' => $user->id,
            'destination'=> 'Test Destination',
            'password'=> 'Test Password',
            'username'=> 'Test Username'
        ]);

        $this->assertInstanceOf(User::class, $Credential->user);
        $this->assertEquals($user->id, $Credential->user->id);
    }
    public function test_credential_belongs_to_role()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create([
            'name' => 'Test Role'
        ]);
        $user->roles()->attach($role);
        $Credential = Credential::factory()->create([
            'user_id' => $user->id,
            'destination'=> 'Test Destination',
            'password'=> 'Test Password',
            'username'=> 'Test Username',
            'role_id'=> $role->id
        ]);

        $this->assertInstanceOf(Role::class, $Credential->role);
        $this->assertEquals($user->roles()->first()->id, $Credential->role->id);
    }
    public function test_credential_might_not_have_role()
    {
        $user = User::factory()->create();
        $Credential = Credential::factory()->create([
            'user_id' => $user->id,
            'destination'=> 'Test Destination',
            'password'=> 'Test Password',
            'username'=> 'Test Username'
        ]);

        $this->assertNull($Credential->role);
        $this->assertDatabaseHas('Credentials', [
            'id' => $Credential->id,
            'role_id' => null,
        ]);
    }
}
