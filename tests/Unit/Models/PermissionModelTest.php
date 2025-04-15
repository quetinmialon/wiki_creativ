<?php

namespace Tests\Unit\Models;

use App\Models\Document;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PermissionModelTest extends TestCase
{
    use DatabaseTransactions;

    public function test_permission_can_be_created()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create();
        $permission = Permission::factory()->create([
            'author' => $user->id,
            'expired_at'=> now()->addDays(7),
            'document_id' => $document->id,
        ]);

        $this->assertDatabaseHas('permissions', [
            'author' => $user->id,
            'expired_at'=> now()->addDays(7),
            'document_id' => $document->id,
        ]);

        $this->assertInstanceOf(Permission::class, $permission);
    }
    public function test_permission_belongs_to_user()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create();
        $permission = Permission::factory()->create([
            'author' => $user->id,
            'expired_at'=> now()->addDays(7),
            'document_id' => $document->id,
        ]);
        $this->assertEquals($user->id, $permission->author);
    }
    public function test_permission_belongs_to_document()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create();
        $permission = Permission::factory()->create([
            'author' => $user->id,
            'expired_at'=> now()->addDays(7),
            'document_id' => $document->id,
        ]);

        $this->assertInstanceOf(Document::class, $permission->document);
        $this->assertEquals($document->id, $permission->document->id);
    }
    public function test_permission_can_be_updated()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create();
        $permission = Permission::factory()->create([
            'author' => $user->id,
            'expired_at'=> now()->addDays(7),
            'document_id' => $document->id,
        ]);

        $permission->update([
            'author' => $user->id,
            'expired_at'=> now()->addDays(7),
            'document_id' => $document->id,
        ]);

        $this->assertDatabaseHas('permissions', [
            'author' => $user->id,
            'expired_at'=> now()->addDays(7),
            'document_id' => $document->id,
        ]);
    }
    public function test_permission_can_be_deleted()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create();
        $permission = Permission::factory()->create([
            'author' => $user->id,
            'expired_at'=> now()->addDays(7),
            'document_id' => $document->id,
        ]);
        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
        ]);
        $permission->delete();

        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }
    public function test_permission_handled_by_belong_to_user()
    {
        $user = User::factory()->create();
        $handler = User::factory()->create();
        $document = Document::factory()->create();
        $permission = Permission::factory()->create([
            'author' => $user->id,
            'expired_at'=> now()->addDays(7),
            'document_id' => $document->id,
            'handled_by' => $handler->id,
        ]);

        $this->assertInstanceOf(User::class, $permission->handledBy);
        $this->assertEquals($handler->id, $permission->handledBy->id);
    }
}
