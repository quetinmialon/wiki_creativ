<?php
namespace Tests\Feature;

use App\Models\Category;
use App\Models\Document;
use App\Models\Favorite;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use DatabaseTransactions;

    private function createUserWithRoleAndDocument(): array
    {
        $user = User::factory()->create(); // Ensure $user is an instance of User
        $user = User::find($user->id); // Explicitly resolve $user to avoid type issues
        $role = Role::create(['name' => 'pédagogie']);
        $user->roles()->attach($role);

        $category = Category::create([
            'name' => 'Category 1',
            'role_id' => $role->id,
        ]);

        $document = Document::create([
            'name' => 'Test Document',
            'content' => 'Some content',
            'excerpt' => 'Excerpt',
            'created_by' => $user->id,
        ]);
        $document->categories()->attach($category->id);

        return [$user, $document];
    }

    public function test_user_can_add_a_document_to_favorites()
    {
        [$user, $document] = $this->createUserWithRoleAndDocument();
        $this->actingAs($user);

        $response = $this->postJson(route('api.ToggleFavorite', $document->id));
        $response->assertStatus(200)
                 ->assertJson(['favorited' => true]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);
    }

    public function test_user_can_remove_a_document_from_favorites()
    {
        [$user, $document] = $this->createUserWithRoleAndDocument();
        Favorite::create(['user_id' => $user->id, 'document_id' => $document->id]);
        $this->actingAs($user);

        $response = $this->postJson(route('api.ToggleFavorite', $document->id));
        $response->assertStatus(200)
                 ->assertJson(['favorited' => false]);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);
    }

    public function test_user_can_view_own_favorites()
    {
        $user = User::factory()->create();
        $user = User::find($user->id);
        $this->actingAs($user);

        $doc1 = Document::factory()->create(['name' => 'Doc A', 'created_by' => $user->id]);
        $doc2 = Document::factory()->create(['name' => 'Doc B', 'created_by' => $user->id]);

        Favorite::factory()->create(['user_id' => $user->id, 'document_id' => $doc1->id]);
        Favorite::factory()->create(['user_id' => $user->id, 'document_id' => $doc2->id]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Doc A');
        $response->assertSee('Doc B');
    }

}
