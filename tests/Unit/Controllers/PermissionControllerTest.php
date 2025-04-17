<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\PermissionController;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Tests\TestCase;
use Mockery;

class PermissionControllerTest extends TestCase
{
    protected $permissionService;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->permissionService = Mockery::mock(PermissionService::class);
        $this->controller = new PermissionController($this->permissionService);
    }

    public function test_index_returns_view_with_permissions()
    {
        $permissions = ['mocked-permissions'];

        $this->permissionService
            ->shouldReceive('getAllPermissions')
            ->once()
            ->andReturn($permissions);

        $response = $this->controller->index();

        $this->assertEquals('permission.permission-list', $response->name());
        $this->assertArrayHasKey('permissions', $response->getData());
        $this->assertEquals($permissions, $response->getData()['permissions']);
    }

    public function test_pending_permissions_returns_view()
    {
        $permissions = ['mocked-pending'];

        $this->permissionService
            ->shouldReceive('getPendingPermissions')
            ->once()
            ->andReturn($permissions);

        $response = $this->controller->pendingPermissions();

        $this->assertEquals('permission.pending-permission-list', $response->name());
        $this->assertArrayHasKey('permissions', $response->getData());
        $this->assertEquals($permissions, $response->getData()['permissions']);
    }


    public function test_create_request_validates_and_redirects()
    {
        $request = Mockery::mock(Request::class);
        $data = [
            'document_id' => 1,
            'expired_at' => '2025-05-01',
            'comment' => 'Commentaire test'
        ];

        $request->shouldReceive('validate')->once()->andReturn($data);
        $request->shouldReceive('all')->once()->andReturn($data);

        $this->permissionService
            ->shouldReceive('createPermissionRequest')
            ->once()
            ->with($data);

        $response = $this->controller->createRequest($request);

        $this->assertEquals(
            route('documents.allDocumentsInfo'),
            $response->getTargetUrl()
        );
        $this->assertEquals('Demande de permission créée avec succès.', $response->getSession()->get('success'));
    }

    public function test_request_form_document_not_found_redirects()
    {
        $this->permissionService
            ->shouldReceive('getDocumentById')
            ->with(999)
            ->andReturn(null);

        Redirect::shouldReceive('route')
            ->with('documents.index')
            ->andReturnSelf();
        Redirect::shouldReceive('with')->with('error', 'Document introuvable')->andReturnSelf();

        $response = $this->controller->requestForm(999);
        $this->assertNotNull($response);
    }

    public function test_request_form_returns_view()
    {
        $documentId = 1;
        $document = (object)['id' => $documentId]; // Simule un objet document

        $this->permissionService
            ->shouldReceive('getDocumentById')
            ->once()
            ->with($documentId)
            ->andReturn($document);

        $response = $this->controller->requestForm($documentId);
        $this->assertEquals('permission.request-form', $response->name());
        $this->assertArrayHasKey('document', $response->getData());
        $this->assertEquals($document, $response->getData()['document']);
    }

    public function test_request_form_redirects_when_document_not_found()
    {
        $this->permissionService
            ->shouldReceive('getDocumentById')
            ->once()
            ->with(99)
            ->andReturn(null);

        $response = $this->controller->requestForm(99);

        $this->assertEquals(route('documents.index'), $response->getTargetUrl());
        $this->assertEquals('Document introuvable', $response->getSession()->get('error'));
    }



    public function test_handle_request_with_success()
    {
        $request = Request::create('/permissions/handle/1', 'POST', [
            'status' => 'approved'
        ]);

        $this->permissionService
            ->shouldReceive('handlePermissionRequest')
            ->once()
            ->with(1, 'approved')
            ->andReturn(['status' => 'approved']);

        Redirect::shouldReceive('route')
            ->with('admin.permissions.pendings')
            ->andReturnSelf();
        Redirect::shouldReceive('with')
            ->with('success', 'Demande de permission modifiée avec succès.')
            ->andReturnSelf();

        $response = $this->controller->handleRequest($request, 1);
        $this->assertNotNull($response);
    }

    public function test_handle_request_not_found()
    {
        $request = Request::create('/permissions/handle/1', 'POST', [
            'status' => 'denied'
        ]);

        $this->permissionService
            ->shouldReceive('handlePermissionRequest')
            ->once()
            ->with(1, 'denied')
            ->andReturn(null);

        Redirect::shouldReceive('route')
            ->with('admin.permissions.pendings')
            ->andReturnSelf();
        Redirect::shouldReceive('with')
            ->with('error', 'Demande de permission introuvable.')
            ->andReturnSelf();

        $response = $this->controller->handleRequest($request, 1);
        $this->assertNotNull($response);
    }

    public function test_destroy_success()
    {
        $this->permissionService
            ->shouldReceive('deletePermission')
            ->once()
            ->with(1)
            ->andReturn(true);

        Redirect::shouldReceive('route')
            ->with('admin.permissions.pendings')
            ->andReturnSelf();
        Redirect::shouldReceive('with')
            ->with('success', 'Demande de permission supprimée avec succès.')
            ->andReturnSelf();

        $response = $this->controller->destroy(1);
        $this->assertNotNull($response);
    }

    public function test_destroy_not_found()
    {
        $this->permissionService
            ->shouldReceive('deletePermission')
            ->once()
            ->with(1)
            ->andReturn(false);

        Redirect::shouldReceive('route')
            ->with('admin.permissions.pendings')
            ->andReturnSelf();
        Redirect::shouldReceive('with')
            ->with('error', 'Demande de permission introuvable.')
            ->andReturnSelf();

        $response = $this->controller->destroy(1);
        $this->assertNotNull($response);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
