<?php

use App\Models\Role;
use App\Models\User;
use App\Models\Category;
use App\Models\Document;
use App\Models\User\UserInvitation;
use App\Models\User\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationLinkMail;
use App\Mail\RejectionMail;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function (): void {
    Mail::fake();
});

it('handles full subscription request flow from user request to account creation', function (): void {
    // User ask for subscription
    $postData = ['name' => 'Alice Dupont', 'email' => 'alice@example.com'];
    $this->post(route('subscribe.store'), $postData)->assertRedirect(route('login'));

    $this->assertDatabaseHas('user_requests', ['email' => 'alice@example.com']);

    //arrange userRequest
    $roles = Role::factory()->count(2)->create();
    $userRequest = UserRequest::where('email', 'alice@example.com')->first();

    //arrange admin account
    $admin = User::factory()->create();
    $adminRole = Role::factory()->create(['name' => 'superadmin']);
    $admin->roles()->attach($adminRole);
    $this->actingAs($admin);

    //Admin accepts the user request
    $this->post(route('subscribe.process', $userRequest->id), [
        'action' => 'accept',
        'role_ids' => $roles->pluck('id')->toArray(),
    ])->assertRedirect();

    // check if invitation has been created
    $this->assertDatabaseHas('user_invitations', [
        'email' => 'alice@example.com'
    ]);
    $invitation = UserInvitation::where('email', 'alice@example.com')->first();
    $this->expect($invitation)->not->toBeNull();

    //logout the admin
    $this->post(route('logout'));

    //User completes registration
    $this->post(route('register.finalization', ['token' => $invitation->token]), [
        'email' => 'alice@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'token' => $invitation->token,
    ])->assertRedirect(route('login'));
    $this->assertDatabaseHas('users', [
        'email' => 'alice@example.com'
    ]);
    $user = User::where('email', 'alice@example.com')->first();
    $this->expect(Hash::check('password123', $user->password))->toBeTrue();
    $this->expect($user->roles)->toHaveCount(3); // 2 roles from invitation + 1 default user role
});

it('handle full document creation and publication with a category creation from form to display on proper user', function(): void{
    // arrange actors
    $authorisedUser = User::factory()->create();
    $author = User::factory()->create();
    $unauthorisedUser = User::factory()->create();
    $qualityMember = User::factory()->create();
    $qualityRole = Role::create(['name'=> 'qualité']);
    $role = Role::create(['name'=> 'pédagogie']);
    $author->roles()->attach($role);
    $authorisedUser->roles()->attach($role);
    $qualityMember->roles()->attach($qualityRole);
    $expectedDocument = [
        'name' => 'Test Document',
        'content' => 'This is a test document content.',
        'excerpt' => 'Test excerpt',
        'created_by' => $author->id,
    ];

    //creating the category
    $this->actingAs($author);
    $this->post(route('myCategories.store'), [
        'name' => 'Test Category',
        'role_id' => $role->id,
    ]);
    //assert category creation
    $this->assertDatabaseHas('categories', [
        'name' => 'Test Category',
        'role_id' => $role->id,
    ]);
    $category = Category::where('name', 'Test Category')->first();
    $expectedDocument['categories_id'] = [$category->id];

    //creating the document
    $this->post(route('documents.store'), $expectedDocument);
    //assert document creation
    $this->assertDatabaseHas('documents', [
        'name' => 'Test Document',
        'excerpt' => 'Test excerpt',
        'created_by' => $author->id,
    ]);
    $document = Document::where('name', 'Test Document')->first();

    //assert user can't access document before approval
    $this->actingAs($authorisedUser);
    $this->get(route('documents.show', $document->id))
        ->assertRedirect(); // document unavailable

    //acting as quality member to approve the document
    $this->actingAs($qualityMember);
    $response = $this->put(route('qualite.addNormedName', $document->id),[
        'id'=> $document->id,
        'formated_name' => 'test_document',
    ]);
    //assert document is approved and normed name is added
    $this->assertDatabaseHas('documents',[
        'id' => $document->id,
        'formated_name' => 'test_document',
    ]);

    //assert user can access document after approval
    $this->actingAs($authorisedUser);
    $this->get(route('documents.show', $document->id))
        ->assertOk(); // document should be accessible

    //assert unauthorised user can't access the document
    $this->actingAs($unauthorisedUser);
    $this->get(route('documents.show', $document->id))
        ->assertForbidden(); // should be forbidden
});
