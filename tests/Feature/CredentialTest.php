<?php

use App\Models\Credential;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Crypt;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('store credential', function (): void {
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
    expect(Crypt::decryptString($credential->password))->toEqual('password123');
});

test('store requires authentication', function (): void {
    $this->post(route('credentials.store'), [
        'destination' => 'example.com',
        'username' => 'testuser',
        'password' => 'password123',
    ])
        ->assertRedirect();
});

test('index shows user credentials and shared ones', function (): void {
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
});

test('edit shows edit form', function (): void {
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
});

test('edit prevents unauthorized access', function (): void {
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
});

test('update credential', function (): void {
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
    expect($credential->destination)->toEqual('new-example.com');
    expect($credential->username)->toEqual('updateduser');
    expect(Crypt::decryptString($credential->password))->toEqual('newpassword123');
});

test('destroy credential', function (): void {
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
});

test('destroy prevents unauthorized access', function (): void {
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
});

test('personnal credentials arent display to others users', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $role = Role::factory()->create(['name' => 'pédagogie']);
    $user->roles()->attach($role->id);
    $otherUser->roles()->attach($role->id);
    $otherUser = User::find($otherUser->id);
    // Ensure $otherUser is an instance of User
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
});
test('shared credentials are displayed to users with the proper role', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $thirdUser = User::factory()->create();

    $role = Role::factory()->create(['name' => 'pédagogie']);
    $user->roles()->attach($role->id);
    $otherUser->roles()->attach($role->id);
    $user = User::find($user->id);
    // Ensure $user is an instance of User
    $otherUser = User::find($otherUser->id);
    // Ensure $otherUser is an instance of User
    $thirdUser = User::find($thirdUser->id);
    // Ensure $thirdUser is an instance of User
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
});