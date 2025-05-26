<?php

use App\Events\DocumentOpened;
use App\Models\Document;
use App\Models\Log;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);
uses(\Illuminate\Foundation\Testing\WithFaker::class);

test('add log dispatches event', function (): void {
    Event::fake();

    $user = User::factory()->create();
    $document = Document::factory()->create([
        'created_by' => $user->id,
        'formated_name' => 'test_name'
    ]);

    $user = User::find($user->id);
    // Ensure $user is an instance of User
    $this->actingAs($user);
    $this->get(route('documents.show', $document));

    Event::assertDispatched(DocumentOpened::class, function ($event) use ($document, $user) {
        return $event->documentId === $document->id && $event->userId === $user->id;
    });
});

test('every logs displays all logs', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $doc1 = Document::factory()->create(['created_by' => $user1->id]);
    $doc2 = Document::factory()->create(['created_by' => $user2->id]);

    $log1 = Log::factory()->create(['user_id' => $user1->id, 'document_id' => $doc1->id]);
    $log2 = Log::factory()->create(['user_id' => $user2->id, 'document_id' => $doc2->id]);

    $admin = User::factory()->create();
    $superAdminRole = Role::create(['name' => 'superadmin']);
    $admin->roles()->attach($superAdminRole);
    $admin = User::find($admin->id);
    // Ensure $admin is an instance of User
    $this->actingAs($admin);

    $response = $this->get(route('everyLogs'));

    $response->assertStatus(200);
    $response->assertViewHas('logs', fn($logs) => $logs->contains($log1) && $logs->contains($log2));
});

test('last opened documents returns last five logs for user', function (): void {
    $user = User::factory()->create();
    $user = User::find($user->id);
    // Ensure $user is an instance of User
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
});