<?php

namespace Tests\Unit\Models;

use App\Models\Role;
use App\Models\User;
use App\Models\User\UserInvitation;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RoleModelTest extends TestCase
{
    use DatabaseTransactions;

    public function test_role_can_be_created()
    {
        $role = Role::factory()->create([
            'name' => 'admin',
        ]);

        $this->assertDatabaseHas('roles', [
            'name' => 'admin',
        ]);

        $this->assertInstanceOf(Role::class, $role);
    }
    public function test_role_can_be_updated()
    {
        $role = Role::factory()->create([
            'name' => 'admin',
        ]);

        $role->update(['name' => 'superadmin']);

        $this->assertDatabaseHas('roles', [
            'name' => 'superadmin',
        ]);
    }
    public function test_role_can_be_deleted()
    {
        $role = Role::factory()->create([
            'name' => 'admin',
        ]);

        $role->delete();

        $this->assertDatabaseMissing('roles', [
            'name' => 'admin',
        ]);
    }
    public function test_role_has_users()
    {
        $role = Role::factory()->create([
            'name' => 'admin',
        ]);

        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->assertInstanceOf(User::class, $role->users->first());
        $this->assertEquals($user->id, $role->users->first()->id);
    }
    public function test_role_has_categories()
    {
        $role = Role::factory()->create([
            'name' => 'admin',
        ]);

        $category = Category::factory()->create([
            'role_id' => $role->id,
        ]);

        $this->assertInstanceOf(Category::class, $role->categories->first());
        $this->assertEquals($category->id, $role->categories->first()->id);
    }
}

