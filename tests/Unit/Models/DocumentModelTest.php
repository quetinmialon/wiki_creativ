<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Document;
use App\Models\Favorite;
use App\Models\Log;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DocumentModelTest extends TestCase
{
    use DatabaseTransactions;

    public function test_document_can_be_created()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create([
            'name' => 'Test Doc',
            'excerpt' => 'Résumé',
            'content' => 'Contenu principal',
            'formated_name' => 'test-doc',
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('documents', [
            'name' => 'Test Doc',
            'excerpt' => 'Résumé',
            'created_by' => $user->id,
        ]);

        $this->assertInstanceOf(Document::class, $document);
    }

    public function test_document_can_be_soft_deleted()
    {
        $document = Document::factory()->create();
        $document->delete();

        $this->assertSoftDeleted('documents', ['id' => $document->id]);
    }

    public function test_document_belongs_to_author()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $document->author);
        $this->assertEquals($user->id, $document->author->id);
    }

    public function test_document_belongs_to_updator()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create(['updated_by' => $user->id]);

        $this->assertInstanceOf(User::class, $document->updator);
        $this->assertEquals($user->id, $document->updator->id);
    }

    public function test_document_has_many_logs()
    {
        $document = Document::factory()->create();
        Log::factory()->count(2)->create(['document_id' => $document->id]);

        $this->assertCount(2, $document->logs);
        $this->assertInstanceOf(Log::class, $document->logs->first());
    }

    public function test_document_has_many_permissions()
    {
        $document = Document::factory()->create();
        Permission::factory()->count(3)->create(['document_id' => $document->id, 'expired_at' => now()->addDays(7)]);

        $this->assertCount(3, $document->permissions);
        $this->assertInstanceOf(Permission::class, $document->permissions->first());
    }

    public function test_document_has_many_favorites()
    {
        $document = Document::factory()->create();
        Favorite::factory()->count(2)->create(['document_id' => $document->id]);

        $this->assertCount(2, $document->favorites);
        $this->assertInstanceOf(Favorite::class, $document->favorites->first());
    }

    public function test_document_belongs_to_many_categories()
    {
        $document = Document::factory()->create();
        $categories = Category::factory()->count(2)->create();
        $document->categories()->attach($categories->pluck('id'));

        $this->assertCount(2, $document->categories);
        $this->assertInstanceOf(Category::class, $document->categories->first());
    }
}
