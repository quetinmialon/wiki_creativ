<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test creating a new category.
     */
    public function test_create_category_while_user_is_superadmin(): void
    {
        $user = User::factory()->create();
        $user = User::find($user->id); // Ensure $user is an instance of User
        $role = Role::factory() ->create([
            "name"=> "superadmin"
        ]);
        $user->roles()->attach($role);
        $this->actingAs($user);
        $response = $this->post(route('categories.store'), [
            'name' => 'Test Category',
            'role_id' => $role->id,
        ]);
        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'role_id' => $role->id,
        ]);
        $response->assertStatus(302)
            ->assertRedirect(route('categories.index'));
        $category = Category::where('name', 'Test Category')->first();
        $this->assertNotNull($category);
        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('Test Category', $category->name);
        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'role_id' => $role->id,
        ]);
    }

    /**
     * Test updating a category.
     */
    public function test_update_category_when_user_is_superadmin(): void
    {
        $user = User::factory()->create();
        $user = User::find($user->id); // Ensure $user is an instance of User
        $role = Role::factory()->create([
            "name"=> "superadmin"
        ]);
        $user->roles()->attach($role);
        $this->actingAs($user);
        $category = Category::create([
            'name' => 'Test Category',
            'role_id' => $role->id,
        ]);
        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'role_id' => $role->id,
        ]);
        $response = $this->put(route('categories.update', $category), [
            'name' => 'Updated Test Category',
            'role_id' => $role->id,
        ]);
        $response->assertStatus(302)
            ->assertRedirect(route('categories.index'));
        $this->assertDatabaseMissing('categories', [
            'name' => 'Test Category',
            'role_id' => $role->id,
        ]);
        $this->assertDatabaseHas('categories', [
            'name' => 'Updated Test Category',
            'role_id' => $role->id,
        ]);
    }
    /**
     * Test deleting a category.
     */
    public function test_delete_category_when_user_is_superadmin(): void
    {
        $user = User::factory()->create();
        $user = User::find($user->id); // Ensure $user is an instance of User
        $role = Role::factory()->create([
            "name"=> "superadmin"
        ]);
        $user->roles()->attach($role);
        $this->actingAs($user);
        $category = Category::create([
            'name' => 'Test Category',
            'role_id' => $role->id,
        ]);
        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'role_id' => $role->id,
        ]);
        $response = $this->delete(route('categories.destroy', $category));
        $response->assertStatus(302)
            ->assertRedirect(route('categories.index'));
        $this->assertDatabaseMissing('categories', [
            'name' => 'Test Category',
            'role_id' => $role->id,
        ]);
        $this->assertNull(Category::find($category->id));
    }

    public function test_create_category_on_regular_role()
    {
        $user = User::factory()->create();
        $user = User::find($user->id); // Ensure $user is an instance of User
        $role = Role::factory()->create([
            "name"=> "pédagogie"
        ]);
        $user->roles()->attach($role);
        $this->actingAs($user);
        $response = $this->post(route('myCategories.store'), [
            'name' => 'Test Category',
            'role_id' => $role->id,
        ]);
        $response->assertStatus(302)
            ->assertRedirect(route('myCategories.myCategories'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'role_id' => $role->id,
        ]);
        $category = Category::where('name', 'Test Category')->first();
        $this->assertNotNull($category);
        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('Test Category', $category->name);
    }

    public function test_only_admin_can_update_categories()
    {
        $user = User::factory()->create();
        $user = User::find($user->id); // Ensure $user is an instance of User
        $role = Role::factory()->create([
            "name"=> "pédagogie"
        ]);
        $user->roles()->attach($role);
        $this->actingAs($user);
        $category = Category::create([
            'name' => 'Test Category',
            'role_id' => $role->id,
        ]);
        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'role_id' => $role->id,
        ]);
        $response = $this->put(route('categories.update', $category), [
            'name' => 'Updated Test Category',
            'role_id' => $role->id,
        ]);
        $response->assertStatus(403);
        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'role_id' => $role->id,
        ]);
        $this->assertDatabaseMissing('categories', [
            'name' => 'Updated Test Category',
            'role_id' => $role->id,
        ]);
        $this->assertEquals('Test Category', $category->name);

        $AdminRole = Role::factory()->create([
            "name"=> "Admin $role->name"
        ]);
        $user->roles()->attach($AdminRole);
        $this->actingAs($user);
        $response = $this->put(route('myCategories.update', $category), [
            'name' => 'Updated Test Category',
            'role_id' => $AdminRole->id,
        ]);
        $response->assertStatus(302)
            ->assertRedirect(route('myCategories.myCategories'));
        $this->assertDatabaseMissing('categories', [
            'name' => 'Test Category',
            'role_id' => $AdminRole->id,
        ]);
        $this->assertDatabaseHas('categories', [
            'name' => 'Updated Test Category',
            'role_id' => $AdminRole->id,
        ]);
    }
    public function test_only_admin_can_delete_categories()
    {
        $user = User::factory()->create();
        $user = User::find($user->id); // Ensure $user is an instance of User
        $role = Role::factory()->create([
            "name"=> "pédagogie"
        ]);
        $user->roles()->attach($role);
        $this->actingAs($user);
        $category = Category::create([
            'name' => 'Test Category',
            'role_id' => $role->id,
        ]);
        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'role_id' => $role->id,
        ]);
        $response = $this->delete(route('myCategories.destroy', $category));
        $response->assertStatus(403);
        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'role_id' => $role->id,
        ]);
        $this->assertNotNull(Category::find($category->id));
        $AdminUser = User::factory()->create();
        $AdminUser = User::find($AdminUser->id); // Ensure $AdminUser is an instance of User
        $AdminRole = Role::factory()->create([
            "name"=> "Admin $role->name"
        ]);
        $AdminUser->roles()->attach($AdminRole);
        $this->actingAs($AdminUser);
        $response = $this->delete(route('myCategories.destroy', $category));
        $response->assertStatus(302)
            ->assertRedirect(route('myCategories.myCategories'));
        $this->assertDatabaseMissing('categories', [
            'name' => 'Test Category',
            'role_id' => $AdminRole->id,
        ]);
        $this->assertNull(Category::find($category->id));
    }
}
