<?php

use App\Models\User;
use App\Models\user\UserRequest;


uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('non authenticated user cannot access admin routes', function (): void {
    $response = $this->get('/admin');
    $response->assertStatus(401);
});

test('non superadmin user forbidden from admin', function (): void {
    $user = User::factory()->create();
    $user->roles()->attach(1);

    $response = $this->actingAs($user)->get('/admin');
    $response->assertStatus(403);
});

test('superadmin can view admin index', function (): void {
    $admin = User::factory()->create();
    $admin->roles()->attach(2);

    $response = $this->actingAs($admin)->get('/admin');
    $response->assertStatus(200)
             ->assertViewIs('admin.admin_index');
});

test('user list displays only non supervisor users', function (): void {
    $admin = User::factory()->create();
    $admin->roles()->attach(2);

    $u1 = User::factory()->create(['name' => 'UserOne']);
    $u1->roles()->attach(1);

    $u2 = User::factory()->create(['name' => 'Supervisor']);
    $u2->roles()->attach(13);

    $response = $this->actingAs($admin)->get('/admin/users');
    $response->assertStatus(200)
             ->assertViewIs('admin.user_list')
             ->assertViewHas('users', function ($users) use ($u1, $u2) {
                 return $users->contains($u1) && !$users->contains($u2);
             });
});

test('edit users role form shows user and roles', function (): void {
    $admin = User::factory()->create();
    $admin->roles()->attach(2);

    $user = User::factory()->create();
    $user->roles()->attach(1);

    $response = $this->actingAs($admin)->get("/admin/users/{$user->id}/edit");
    $response->assertStatus(200)
             ->assertViewIs('admin.edit_roles_form')
             ->assertViewHasAll(['user', 'roles']);
});

test('revoke nonexistent user redirects with error', function (): void {
    $admin = User::factory()->create();
    $admin->roles()->attach(2);

    $response = $this->actingAs($admin)->delete('/admin/users/999');
    $response->assertRedirect(route('admin.users'))
             ->assertSessionHas('error', "l'utilisateur n'exite pas en base de donnée");
});

test('cannot revoke superadmin user', function (): void {
    $admin = User::factory()->create();
    $admin->roles()->attach(2);

    $user = User::factory()->create();
    $user->roles()->attach(2);

    $response = $this->actingAs($admin)->delete("/admin/users/{$user->id}");
    $response->assertRedirect(route('admin.users'))
             ->assertSessionHas('error', "l'utilisateur que vous souhaitez supprimer est un superadmin, vous ne pouvez pas le supprimer");
});

test('cannot revoke supervisor user', function (): void {
    $admin = User::factory()->create();
    $admin->roles()->attach(2);

    $user = User::factory()->create();
    $user->roles()->attach(13);

    $response = $this->actingAs($admin)->delete("/admin/users/{$user->id}");
    $response->assertRedirect(route('admin.users'))
             ->assertSessionHas('error', "l'utilisateur que vous tentez de supprimer n'existe pas");
});

test('can revoke regular user', function (): void {
    $admin = User::factory()->create();
    $admin->roles()->attach(2);

    $user = User::factory()->create();
    $user->roles()->attach(1);

    $response = $this->actingAs($admin)->delete("/admin/users/{$user->id}");
    $response->assertRedirect(route('admin.users'))
             ->assertSessionHas('success', 'Utilisateur retiré avec succès.');
    expect(User::find($user->id))->toBeNull();
});

test('update user role validation and errors', function (): void {
    $admin = User::factory()->create();
    $admin->roles()->attach(2);

    $user = User::factory()->create();
    $user->roles()->attach(1);

    // invalid role id 13 triggers custom error
    $response = $this->actingAs($admin)->put("/admin/users/{$user->id}", [
        'id' => $user->id,
        'roles' => [13]
    ]);
    $response->assertSessionHasErrors();
});

test('cannot update superadmin role when not current user', function (): void {
    $admin = User::factory()->create();
    $admin->roles()->attach(2);

    $another = User::factory()->create();
    $another->roles()->attach(2);

    $response = $this->actingAs($admin)->put("/admin/users/{$another->id}", [
        'id' => $another->id,
        'roles' => [2]
    ]);
    $response->assertSessionHasErrors(['roles']);
});

test('cannot update supervisor user roles', function (): void {
    $admin = User::factory()->create();
    $admin->roles()->attach(2);

    $sup = User::factory()->create();
    $sup->roles()->attach(13);

    $response = $this->actingAs($admin)->put("/admin/users/{$sup->id}", [
        'id' => $sup->id,
        'roles' => [1]
    ]);
    $response->assertSessionHasErrors(['roles']);
});

test('can update user roles successfully', function (): void {
    $admin = User::factory()->create();
    $admin->roles()->attach(2);

    $user = User::factory()->create();
    $user->roles()->attach(1);

    $response = $this->actingAs($admin)->put("/admin/users/{$user->id}", [
        'id' => $user->id,
        'roles' => [4]
    ]);
    $response->assertRedirect(route('admin.users'))
             ->assertSessionHas('success', 'Rôle utilisateur mis à jour avec succès.');
    expect($user->fresh()->roles->contains('id', 4))->toBeTrue();
});

test('role list displays all roles', function (): void {
    $admin = User::factory()->create();
    $admin->roles()->attach(2);

    $response = $this->actingAs($admin)->get('/admin/roles');
    $response->assertStatus(200)
             ->assertViewIs('admin.role_list')
             ->assertViewHas('roles', function ($roles) {
                 return $roles->count() >= 4;
             });
});

test('user requests view shows pending requests and roles', function (): void {
    $admin = User::factory()->create();
    $admin->roles()->attach(2);

    UserRequest::factory()->create(['status' => 'pending']);

    $response = $this->actingAs($admin)->get('/admin/requests');
    $response->assertStatus(200)
             ->assertViewIs('admin.user_request_list')
             ->assertViewHasAll(['userRequests', 'roles']);
});

test('search user redirects when no results', function (): void {
    $admin = User::factory()->create();
    $admin->roles()->attach(2);

    $response = $this->actingAs($admin)->post('/admin/users/searchDocument', ['query' => 'nonexistent']);
    $response->assertRedirect(route('admin.users'))
             ->assertSessionHas('error', 'Aucun résultat trouvé');
});

test('search user displays results when found', function (): void {
    $admin = User::factory()->create();
    $admin->roles()->attach(2);

    $user = User::factory()->create(['name' => 'SpecificName']);

    $response = $this->actingAs($admin)->post('/admin/users/searchDocument', ['query' => 'Specific']);
    $response->assertStatus(200)
             ->assertViewIs('admin.search_users_results')
             ->assertViewHas('users', function ($users) use ($user) {
                 return $users->contains($user);
             });
});
