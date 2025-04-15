<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Document;
use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CategoryModelTest extends TestCase
{
    use DatabaseTransactions;

    public function test_category_can_be_created()
    {
        $category = Category::factory()->create([
            'name' => 'Test Category',
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
        ]);

        $this->assertInstanceOf(Category::class, $category);
    }

    public function test_category_can_be_deleted()
    {
        $category = Category::factory()->create();
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
        $category->delete();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
    public function test_category_can_be_updated()
    {
        $category = Category::factory()->create([
            'name' => 'Old Name',
        ]);

        $category->update([
            'name' => 'New Name',
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'New Name',
        ]);
    }
    public function test_category_belongs_to_role()
    {
        $role = Role::factory()->create();
        $category = Category::factory()->create(['role_id' => $role->id]);

        $this->assertInstanceOf(Role::class, $category->role);
        $this->assertEquals($role->id, $category->role->id);
    }
    public function test_category_has_many_documents()
    {
        $category = Category::factory()->create();
        $document1 = Document::factory()->create();
        $document2 = Document::factory()->create();
        $category->documents()->attach($document1->id);
        $category->documents()->attach($document2->id);
        $this->assertDatabaseHas('category_document', [
            'category_id' => $category->id,
            'document_id' => $document1->id,
        ]);
        $this->assertDatabaseHas('category_document', [
            'category_id' => $category->id,
            'document_id' => $document2->id,
        ]);
        $this->assertCount(2, Category::with('documents')->find($category->id)->documents);
        $this->assertCount(2, $category->documents);
        $this->assertInstanceOf(Document::class, Category::with('documents')->find($category->id)->documents->first());
        $this->assertInstanceOf(Document::class, $category->documents->first());
        $this->assertEquals($document1->id, Category::with('documents')->find($category->id)->documents[0]->id);
        $this->assertEquals($document1->id, $category->documents[0]->id);
    }
}
