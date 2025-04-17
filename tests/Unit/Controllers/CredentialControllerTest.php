<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use Mockery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\CredentialService;
use App\Http\Controllers\CredentialController;
use App\Models\Credential;
use App\Models\User;

class CredentialControllerTest extends TestCase
{
    protected $credentialService;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->credentialService = Mockery::mock(CredentialService::class);
        $this->controller = new CredentialController($this->credentialService);
    }

    public function test_store_saves_credential_and_redirects_back()
    {
        $request = Mockery::mock(Request::class);
        $data = ['destination' => 'Test', 'username' => 'user', 'password' => 'secret', 'role_id' => null];
        $request->shouldReceive('validate')->andReturn($data);
        $request->shouldReceive('all')->andReturn($data);

        $this->credentialService
            ->shouldReceive('storeCredential')
            ->once()
            ->with($data)
            ->andReturn(['success' => 'logs créés avec succès']);

        $response = $this->controller->store($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('logs créés avec succès', session()->get('success'));
    }

    public function test_create_returns_view_if_user_has_roles()
    {
        $this->credentialService->shouldReceive('getUserRoles')->andReturn(['admin']);

        $response = $this->controller->create();

        $this->assertEquals('credentials.create-credentials', $response->getName());
        $this->assertArrayHasKey('roles', $response->getData());
    }

    public function test_create_redirects_if_no_roles()
    {
        $this->credentialService->shouldReceive('getUserRoles')->andReturn(null);

        $response = $this->controller->create();

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('vous devez être connecté pour ajouter des logs', session()->get('error'));
    }

    public function test_index_returns_view_with_credentials()
    {
        $this->credentialService->shouldReceive('getUserCredentials')->andReturn(['credentials' => []]);

        $response = $this->controller->index();

        $this->assertEquals('credentials.credentials', $response->getName());
    }

    public function test_index_redirects_on_error()
    {
        $this->credentialService->shouldReceive('getUserCredentials')->andReturn(['error' => 'not allowed']);

        $response = $this->controller->index();

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('not allowed', session()->get('error'));
    }
}
