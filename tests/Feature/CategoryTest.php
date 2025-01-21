<?php

namespace Tests\Feature;

use App\Models\Category;
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
        $category = Category::create([
                "name"=> "Test Category",
                'role_id' => 1
            ]);
        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('Test Category', $category->name);
        $this->assertEquals(1, $category->role_id);
        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'role_id' => 1,
        ]);
    }

    /**
     * Test updating a category.
     */
    public function test_update_category(): void
    {
        $category = Category::create([
            "name"=> "Test Category",
            'role_id' => 1
        ]);
        $category->update([
            'name' => 'Updated Test Category',
            'role_id' => 2
        ]);
        $this->assertEquals('Updated Test Category', $category->fresh()->name);
        $this->assertEquals(2, $category->fresh()->role_id);
        $this->assertDatabaseHas('categories', [
            'name' => 'Updated Test Category',
            'role_id' => 2,
        ]);
    }
    /**
     * Test deleting a category.
     */
    public function test_delete_category(): void
    {
        $category = Category::create([
            "name"=> "Test Category",
            'role_id' => 1
        ]);
        $category->delete();
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }
    
}
