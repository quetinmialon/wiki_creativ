<?php

namespace Tests\Feature;

use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;

class RoleTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Testing role creation
     *
     * @return void
     */
    public function test_create_role()
    {
        $data = [
            'name' => 'test',
        ];

        $user = User::factory()->create();
        $user->roles()->attach(Role::factory()->create(['name' => 'superadmin']));
        $user = User::find($user->id); // Ensure $user is an instance of User
        $this->actingAs($user);

        // Sending creation data to the database
        $response = $this->post(route('roles.insert'), $data);

        // check if the data is in the database
        $this->assertDatabaseHas('roles', [
            'name' => 'test',
        ]);

        // check if response is correct
        $response->assertRedirect('/admin/roles');
    }

    public function test_create_role_also_create_admin_role()
    {
        $data = [
            'name' => 'test',
        ];

        $user = User::factory()->create();
        $user->roles()->attach(Role::factory()->create(['name' => 'superadmin']));
        $user = User::find($user->id); // Ensure $user is an instance of User
        $this->actingAs($user);

        // Sending creation data to the database
        $this->post(route('roles.insert'), $data);

        $this->assertDatabaseHas('roles', [
            'name' => 'Admin test',
        ]);
        $this->assertDatabaseHas('roles', [
            'name' => 'test',
        ]);
    }

    /**
     * updating role test.
     *
     * @return void
     */
    public function test_update_role()
    {
        //creating a role
        $role = Role::create(['name' => 'User']);
        //creating new data
        $data = [
            'name' => 'Super Admin',
        ];
        $user = User::factory()->create();
        $user->roles()->attach(Role::factory()->create(['name' => 'superadmin']));
        $user = User::find($user->id); // Ensure $user is an instance of User
        $this->actingAs($user);

        $response = $this->put(route('roles.update', $role->id), $data);

        // check if datas has been merged
        $this->assertDatabaseHas('roles', [
            'name' => 'Super Admin',
        ]);
        $response->assertRedirect('/admin/roles');
    }

    public function test_update_role_also_update_the_admin_associated_one()
    {
        $data = [
            'name' => 'test',
        ];

        $user = User::factory()->create();
        $user->roles()->attach(Role::factory()->create(['name' => 'superadmin']));
        $user = User::find($user->id); // Ensure $user is an instance of User
        $this->actingAs($user);

        // Sending creation data to the database
        $this->post(route('roles.insert'), $data);

        $this->assertDatabaseHas('roles', [
            'name' => 'Admin test',
        ]);
        $this->assertDatabaseHas('roles', [
            'name' => 'test',
        ]);

        $this->actingAs($user);

        $role = Role::where('name', 'test')->first();

        $this->put(route('roles.update', $role->id ), [
            'name' => 'test2',
        ]);


        $this->assertDatabaseHas('roles', [
            'name' => 'Admin test2',
        ]);
        $this->assertDatabaseHas('roles', [
            'name' => 'test2',
        ]);
        $this->assertDatabaseMissing('roles', [
            'name' => 'Admin test',
        ]);
        $this->assertDatabaseMissing('roles', [
            'name' => 'test',
        ]);
    }

    /**
     * suppression role test
     *
     * @return void
     */
    public function test_delete_role()
    {
        // Create new role
        $role = Role::create(['name' => 'Editor']);

        $user = User::factory()->create();
        $user->roles()->attach(Role::factory()->create(['name' => 'superadmin']));
        $user = User::find($user->id); // Ensure $user is an instance of User
        $this->actingAs($user);

        // sending delete request on the previously created role
        $response = $this->delete(route('roles.destroy', $role->id));

        // check if the role has been deleted
        $this->assertDatabaseMissing('roles', [
            'name' => 'Editor',
        ]);
        $response->assertRedirect('/admin/roles');
    }

    /**
     * Test la lecture des rôles (index).
     *
     * @return void
     */
    public function test_list_roles_displays_roles()
    {
        // Creating some roles
        Role::create(['name' => 'test']);
        Role::create(['name' => 'User']);
        $user = User::factory()->create();
        $user->roles()->attach(Role::factory()->create(['name' => 'superadmin']));
        $user = User::find($user->id); // Ensure $user is an instance of User
        $this->actingAs($user);

        // sending get request to get the data from database
        $response = $this->get('/admin/roles');

        // check if response has the data
        $response->assertStatus(200);
        $response->assertSee('test');
        $response->assertSee('User');
    }

    public function test_roles_list_doesnt_display_admin_roles(){
        // Creating some roles
        Role::create(['name' => 'test']);
        Role::create(['name' => 'Admin test']);
        $user = User::factory()->create();
        $user->roles()->attach(Role::factory()->create(['name' => 'superadmin']));
        $user = User::find($user->id); // Ensure $user is an instance of User
        $this->actingAs($user);

        // sending get request to get the data from database
        $response = $this->get('/admin/roles');

        // check if response has the data
        $response->assertStatus(200);
        $response->assertSee('test');
        $response->assertDontSee('Admin test');
    }

    public function test_admin_and_default_roles_can_not_be_deleted()
    {
        // Creating admin and default roles
        $role_admin = Role::create(['name' => 'superadmin']);
        $role_default = Role::create(['name' => 'default']);

        $user = User::factory()->create();
        $user->roles()->attach($role_admin);
        $user = User::find($user->id); // Ensure $user is an instance of User
        $this->actingAs($user);

        // try to delete roles admin and default
        $response_admin = $this->delete(route('roles.destroy', $role_admin->id));
        $response_default = $this->delete(route('roles.destroy', $role_default->id));

        // check if datas are in databases
        $this->assertDatabaseHas('roles', [
            'name' => 'superadmin',
        ]);
        $this->assertDatabaseHas('roles', [
            'name' => 'default',
        ]);


        // check rdirection on each session
        $response_admin->assertRedirect('/admin/roles');
        $response_default->assertRedirect('/admin/roles');

        // check if error message is present
        $response_admin->assertSessionHas('error');
        $response_default->assertSessionHas('error');
    }

    public function test_cores_roles_cant_be_updated(){
        // Creating admin and default roles
        $role_admin = Role::create(['name' => 'superadmin']);
        $role_default = Role::create(['name' => 'default']);

        $user = User::factory()->create();
        $user->roles()->attach($role_admin);
        $user = User::find($user->id); // Ensure $user is an instance of User
        $this->actingAs($user);

        // try to delete roles admin and default
        $response_admin = $this->put(route('roles.update', $role_admin->id), ['name' => 'test']);
        $response_default = $this->put(route('roles.update', $role_default->id), ['name' => 'test']);

        // check if datas are in databases
        $this->assertDatabaseHas('roles', [
            'name' => 'superadmin',
        ]);
        $this->assertDatabaseHas('roles', [
            'name' => 'default',
        ]);
        // check rdirection on each session
        $response_admin->assertRedirect('/admin/roles');
        $response_default->assertRedirect('/admin/roles');
        // check if error message is present
        $response_admin->assertSessionHas('error');
        $response_default->assertSessionHas('error');
    }

}
