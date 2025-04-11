<?php

namespace Tests\Feature;

use App\Events\DocumentOpened;
use App\Models\Document;
use App\Models\Log;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class LogsTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function test_add_log_dispatches_event()
    {
        Event::fake();

        $user = User::factory()->create();
        $document = Document::factory()->create(['created_by' => $user->id]);

        $user = User::find($user->id); // Ensure $user is an instance of User
        $this->actingAs($user);
        $this->get(route('documents.show', $document));

        Event::assertDispatched(DocumentOpened::class, function ($event) use ($document, $user) {
            return $event->documentId === $document->id && $event->userId === $user->id;
        });
    }

    public function test_every_logs_displays_all_logs()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $doc1 = Document::factory()->create(['created_by' => $user1->id]);
        $doc2 = Document::factory()->create(['created_by' => $user2->id]);

        $log1 = Log::factory()->create(['user_id' => $user1->id, 'document_id' => $doc1->id]);
        $log2 = Log::factory()->create(['user_id' => $user2->id, 'document_id' => $doc2->id]);

        $admin = User::factory()->create();
        $superAdminRole = Role::create(['name' => 'superadmin']);
        $admin->roles()->attach($superAdminRole);
        $admin = User::find($admin->id); // Ensure $admin is an instance of User
        $this->actingAs($admin);

        $response = $this->get(route('everyLogs'));

        $response->assertStatus(200);
        $response->assertViewHas('logs', fn($logs) => $logs->contains($log1) && $logs->contains($log2));
    }

    public function test_last_opened_documents_returns_last_five_logs_for_user()
    {
        $user = User::factory()->create();
        $user = User::find($user->id); // Ensure $user is an instance of User
        $this->actingAs($user);

        for ($i = 1; $i <= 6; $i++) {
            $doc = Document::factory()->create(['name' => "Document $i", 'created_by' => $user->id]);
            Log::factory()->create(['user_id' => $user->id, 'document_id' => $doc->id]);
        }

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Document 1');
        $response->assertSee('Document 2');
        $response->assertSee('Document 3');
        $response->assertSee('Document 4');
        $response->assertSee('Document 5');
        $response->assertDontSee('Document 6');
    }
}
