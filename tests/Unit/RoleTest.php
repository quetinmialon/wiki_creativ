<?php

namespace Tests\Unit;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testing role creation
     *
     * @return void
     */
    public function test_create_role()
    {
        $data = [
            'name' => 'Admin',
        ];

        // Sending creation data to the database
        $response = $this->post(route('roles.insert'), $data);

        // check if the data is in the database
        $this->assertDatabaseHas('roles', [
            'name' => 'Admin',
        ]);

        // check if response is correct
        $response->assertRedirect('/');
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

        // merge data on the role
        $response = $this->put(route('roles.update', $role->id), $data);

        // check if datas has been merged
        $this->assertDatabaseHas('roles', [
            'name' => 'Super Admin',
        ]);
        $response->assertRedirect('/');
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

        // sending delete request on the previously created role
        $response = $this->delete(route('roles.destroy', $role->id));

        // check if the role has been deleted
        $this->assertDatabaseMissing('roles', [
            'name' => 'Editor',
        ]);
        $response->assertRedirect('/');
    }

    /**
     * Test la lecture des rôles (index).
     *
     * @return void
     */
    public function test_list_roles()
    {
        // Creating some roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'User']);

        // sending get request to get the data from database
        $response = $this->get('/');

        // check if response has the data
        $response->assertStatus(200);
        $response->assertSee('Admin');
        $response->assertSee('User');
    }
}
