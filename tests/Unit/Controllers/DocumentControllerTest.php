<?php

namespace Tests\Unit\Http\Controllers;

use App\Events\DocumentOpened;
use App\Http\Controllers\DocumentController;
use App\Models\Document;
use App\Models\User;
use App\Services\DocumentService;
use App\Services\LogService;
use App\Services\RoleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

use function PHPSTORM_META\type;

class DocumentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $documentService;
    protected $logService;
    protected $roleService;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

       $this->documentService = Mockery::mock(DocumentService::class);
       $this->logService = Mockery::mock(LogService::class);
       $this->roleService = Mockery::mock(RoleService::class);

       $this->controller = new DocumentController(
           $this->documentService,
           $this->logService,
           $this->roleService
        );
    }

    public function test_index_returns_view()
    {
       $this->documentService
            ->shouldReceive('getAllCategoriesWithDocuments')
            ->once()
            ->andReturn([]);

       $response =$this->controller->index();

       $this->assertEquals('documents.document-list',$response->name());
       $this->assertArrayHasKey('categories',$response->getData());
    }

    public function test_create_returns_form_view()
    {
        $roles = collect(['fake-role']);

        $rolesQuery = Mockery::mock();
        $rolesQuery->shouldReceive('with')->with('categories')->andReturnSelf();
        $rolesQuery->shouldReceive('get')->andReturn($roles);

        $user = Mockery::mock(User::class);
        $user->shouldReceive('roles')->andReturn($rolesQuery);

        Auth::shouldReceive('user')->andReturn($user);

        $controller = new DocumentController(
            $this->documentService,
            $this->logService,
            $this->roleService
        );

        $response = $controller->create();

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('documents.create-form', $response->name());
        $this->assertArrayHasKey('roles', $response->getData());
        $this->assertEquals($roles, $response->getData()['roles']);
    }


    public function test_store_validates_and_redirects()
    {
        $request = Request::create('/store', 'POST', [
        'name' => 'Doc',
        'content' => '# Title is minimum 10chars',
        'excerpt' => 'Extrait',
        'categories_id' => [],
        ]);

        $this->documentService
        ->shouldReceive('createDocument')
        ->once()
        ->with(Mockery::type('array'));

        $response =$this->controller->store($request);

        $this->assertInstanceOf(RedirectResponse::class,$response);
        $this->assertEquals(route('documents.index'),$response->getTargetUrl());
    }

    public function test_show_dispatches_event_and_returns_view()
    {
        Event::fake();

        $user = User::factory()->make(['id' => 1]);
        $this->actingAs($user);

        $document = Mockery::mock(new Document(['id'=>5, 'created_by' => 1]));


        // On simule les autorisations
        Gate::shouldReceive('allows')->with('view-document', $document)->andReturn(true);

        // Mock du DocumentService pour éviter Document::findOrFail()
        $this->documentService
            ->shouldReceive('findDocument')
            ->with(5)
            ->andReturn($document);

        $response = $this->controller->show(5);

        Event::assertDispatched(DocumentOpened::class, function ($event) use ($document, $user) {
            return $event->documentId === $document->id && $event->userId === $user->id;
        });

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('documents.document', $response->name());
    }




    public function test_destroy_deletes_if_authorized()
    {
        $document = Document::factory()->make(['id' => 1, 'created_by' => 1]);

        Auth::shouldReceive('id')->andReturn(1);
        $this->documentService->shouldReceive('findDocument')->with(1)->andReturn($document);
        Gate::shouldReceive('allows')->andReturn(false); // first 2 checks fail

        $this->documentService
        ->shouldReceive('deleteDocument')
        ->once()
        ->with($document);

        $response =$this->controller->destroy(1);

        $this->assertInstanceOf(RedirectResponse::class,$response);
        $this->assertEquals(route('documents.index'),$response->getTargetUrl());
    }

    public function test_edit_returns_edit_view_if_authorized()
    {
        $document = Document::factory()->make();
        Auth::shouldReceive('user')->andReturn((object)['id' => 1]);
        Gate::shouldReceive('forUser')->andReturnSelf();
        Gate::shouldReceive('allows')->andReturn(true);

        $this->documentService->shouldReceive('findDocument')->with($document->id)->andReturn($document);
        $this->roleService->shouldReceive('getRolesWhereCategoriesExist')->andReturn([]);

        $response =$this->controller->edit($document->id);

        $this->assertEquals('documents.edit-form',$response->name());
    }

    public function test_update_validates_and_redirects()
    {
        $document = Document::factory()->make();
        $document->id = 42;

        $this->documentService->shouldReceive('findDocument')->andReturn($document);
        Auth::shouldReceive('user')->andReturn((object)['id' => 1]);
        Gate::shouldReceive('forUser')->andReturnSelf();
        Gate::shouldReceive('allows')->andReturn(true);

        $request = Request::create('/update', 'POST', [
            'name' => 'Updated',
            'content' => '## Content zjbd',
            'excerpt' => 'New excerpt',
            'categories_id' => []
        ]);

        $this->documentService
        ->shouldReceive('updateDocument')
        ->once()
        ->with($document, Mockery::type('array'));
        dump($document->id);
        $response =$this->controller->update($request,$document->id);


        $this->assertEquals(route('documents.show',['document' => $document->id]),$response->getTargetUrl());
    }

    public function test_logs_returns_logs_view()
    {
        $doc = Document::factory()->make();
        $doc->setRelation('logs', collect());

        $this->logService->shouldReceive('getDocumentLogs')->with(5)->andReturn($doc);

        $response =$this->controller->logs(5);

        $this->assertEquals('documents.logs',$response->name());
    }

    public function test_everyLogs_returns_all_logs()
    {
    $this->logService->shouldReceive('getAllLogs')->andReturn([]);

    $response =$this->controller->everyLogs();

    $this->assertEquals('documents.all-logs',$response->name());
    }

    public function test_userLogs_returns_logs()
    {
        $user = new User();
        $user->setRelation('logs', collect());
        $this->logService->shouldReceive('getUserLogs')->with(10)->andReturn($user);

        $response =$this->controller->userLogs(10);

        $this->assertEquals('documents.user-logs',$response->name());
    }

    public function test_getAllDocuments_returns_view()
    {
        $this->documentService->shouldReceive('getEveryDocumentswithoutPagination')->andReturn([]);

        $response =$this->controller->getAllDocuments();

        $this->assertEquals('documents.all-documents',$response->name());
    }

    public function test_AllDocumentsInfo_returns_view()
    {
        $collection = collect(['content' => 'to be excluded']);
        $this->documentService->shouldReceive('getEveryDocumentswithoutPagination')->andReturn($collection);

        $response =$this->controller->AllDocumentsInfo();

        $this->assertEquals('documents.all-documents-info',$response->name());
    }

    public function test_search_returns_results()
    {
        $request = Request::create('/search', 'GET', ['query' => 'example']);

        $this->documentService->shouldReceive('searchDocuments')->with('example')->andReturn(collect(['doc']));

        $response =$this->controller->search($request);

        $this->assertEquals('documents.search-results',$response->name());
    }
}
