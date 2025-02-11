<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Role;
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
    public function test_create_category(): void
    {
        $role = Role::create([
            "name"=> "test"
        ]);
        $category = Category::create([
                "name"=> "Test Category",
                'role_id' => $role->id
        ]);
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
    public function test_update_category(): void
    {
        $role = Role::create([
            "name"=> "test"
        ]);
        $secondRole = Role::create([
            "name"=> "anotherTest",
        ]);
        $category = Category::create([
                "name"=> "Test Category",
                'role_id' => $role->id
        ]);
        $category->update([
            'name' => 'Updated Test Category',
            'role_id' => $secondRole->id
        ]);
        $this->assertEquals('Updated Test Category', $category->fresh()->name);
        $this->assertDatabaseHas('categories', [
            'name' => 'Updated Test Category',
            'role_id' => $secondRole->id,
        ]);
    }
    /**
     * Test deleting a category.
     */
    public function test_delete_category(): void
    {
        $role = Role::create([
            "name"=> "test"
        ]);
        $category = Category::create([
                "name"=> "Test Category",
                'role_id' => $role->id
        ]);
        $category->delete();
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

}
