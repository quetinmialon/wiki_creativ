<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Log;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogsTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function test_logs_returns_logs_for_existing_document()
    {
        // Arrange
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);

        $document = Document::create([
            'name' => 'Document 1',
            'content' => 'Content 1',
            'excerpt' => 'Excerpt 1',
            'created_by' => $user->id,
        ]);

        $log = Log::create([
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);

        // Act
        $response = $this->get(route('documents.logs', $document->id));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('logs', function ($logs) use ($log) {
            return $logs->contains($log);
        });
    }

    public function test_logs_redirects_if_document_not_found()
    {
        // Act
        $response = $this->get(route('documents.logs', 999)); // ID inexistant

        // Assert
        $response->assertRedirect(route('documents.index'));
        $response->assertSessionHas('error', 'Document introuvable');
    }

    public function test_add_log_creates_log_for_existing_document()
    {
        // Arrange
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->actingAs($user);

        $document = Document::create([
            'name' => 'Document 1',
            'content' => 'Content 1',
            'excerpt' => 'Excerpt 1',
            'created_by' => $user->id,
        ]);

        // Act
        $response = $this->post(route('documents.newLog', $document->id));

        // Assert
        $response->assertStatus(302); //redirect after creating logs, might change
        $this->assertDatabaseHas('logs', [
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);
    }

    public function test_add_log_redirects_if_document_not_found()
    {
        // Arrange
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->actingAs($user);

        // Act
        $response = $this->post(route('documents.newLog', 999)); // ID inexistant

        // Assert
        $response->assertRedirect(route('documents.index'));
        $response->assertSessionHas('error', 'Document introuvable');
    }

    public function test_every_logs_displays_all_logs()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);
        $anotherUser = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'password' => bcrypt('password456'),
        ]);
        $document1 = Document::create([
            'name' => 'Document 1',
            'content' => 'Content 1',
            'excerpt' => 'Excerpt 1',
            'created_by' => 1,
        ]);
        $document2 = Document::create([
            'name' => 'Document 2',
            'content' => 'Content 2',
            'excerpt' => 'Excerpt 2',
            'created_by' => 2,
        ]);

        // Arrange
        $log1 = Log::create(['user_id' => $user->id, 'document_id' => $document1->id]);
        $log2 = Log::create(['user_id' => $anotherUser->id, 'document_id' => $document2->id]);

        // Act
        $response = $this->get(route('documents.everyLogs'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('logs', function ($logs) use ($log1, $log2) {
            return $logs->contains($log1) && $logs->contains($log2);
        });
    }

    public function test_user_logs_displays_logs_for_specific_user()
    {
        // Arrange
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);

        $document = Document::create([
            'name' => 'Document 1',
            'content' => 'Content 1',
            'excerpt' => 'Excerpt 1',
            'created_by' => $user->id,
        ]);

        $log = Log::create([
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);

        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertDatabaseHas('logs', ['id' => $log->id]);

        $this->actingAs($user);

        // Act
        $response = $this->get(route('documents.userLogs', $user->id));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('logs', function ($logs) use ($log) {
            return $logs->contains($log);
        });
    }
    public function test_user_logs_redirects_if_user_not_found()
    {
        // Act
        $response = $this->get(route('documents.userLogs', 999)); // ID inexistant

        // Assert
        $response->assertRedirect(route('documents.index'));
        $response->assertSessionHas('error', 'Utilisateur introuvable');
    }

    public function test_last_opened_documents_returns_last_five_logs_for_user()
    {
        // Arrange
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->actingAs($user);

        // Créer 6 logs pour l'utilisateur
        for ($i = 1; $i <= 6; $i++) {
            $document = Document::create([
                'name' => "Document $i",
                'content' => "Content of Document $i",
                'excerpt' => "Excerpt of Document $i",
                'created_by' => $user->id,
            ]);

            Log::create([
                'user_id' => $user->id,
                'document_id' => $document->id,
            ]);
        }

        // Act
        $response = $this->get(route('documents.lastOpened'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('logs', function ($logs) {
            return $logs->count() === 5;
        });
    }
}
