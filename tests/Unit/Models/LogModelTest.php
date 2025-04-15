<?php

namespace Tests\Unit\Models;

use App\Models\Document;
use App\Models\Log;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LogModelTest extends TestCase
{
    use DatabaseTransactions;

    public function test_log_can_be_created()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create();
        $log = Log::factory()->create([
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);

        $this->assertDatabaseHas('logs', [
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);

        $this->assertInstanceOf(Log::class, $log);
    }
    public function test_log_belongs_to_user()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create();
        $log = Log::factory()->create([
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);

        $this->assertInstanceOf(User::class, $log->user);
        $this->assertEquals($user->id, $log->user->id);
    }
    public function test_log_belongs_to_document()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create();
        $log = Log::factory()->create([
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);

        $this->assertInstanceOf(Document::class, $log->document);
        $this->assertEquals($document->id, $log->document->id);
    }

    public function test_log_can_be_updated()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create();
        $log = Log::factory()->create([
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);

        $log->update(['updated_at' => now()]);

        $this->assertDatabaseHas('logs', [
            'id' => $log->id,
            'updated_at' => $log->updated_at,
        ]);
    }

    public function test_log_can_be_deleted()
    {
        $user = User::factory()->create();
        $document = Document::factory()->create();
        $log = Log::factory()->create([
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);

        $log->delete();

        $this->assertDatabaseMissing('logs', [
            'id' => $log->id,
        ]);
    }
}
