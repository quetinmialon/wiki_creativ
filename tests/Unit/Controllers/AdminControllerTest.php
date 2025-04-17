<?php
namespace Tests\Unit\Controllers;
use Tests\TestCase;
use Mockery;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Services\UserService;
use App\Services\RoleService;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminControllerTest extends TestCase
{
    protected $userService;
    protected $roleService;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = Mockery::mock(UserService::class);
        $this->roleService = Mockery::mock(RoleService::class);
        $this->controller = new AdminController($this->userService, $this->roleService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    public function test_index_returns_view()
    {
        $response = $this->controller->index();

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('admin.admin_index', $response->getName());
    }
    public function test_user_list_returns_view_with_users()
    {
        $users = collect(['user1', 'user2']);
        $this->userService
            ->shouldReceive('getAllUsersWithRoles')
            ->once()
            ->andReturn($users);

        $response = $this->controller->UserList();

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('admin.user_list', $response->getName());
        $this->assertEquals($users, $response->gatherData()['users']);
    }

    public function test_edit_users_role_returns_view_with_user_and_roles()
    {
        $fakeUser = (object)['id' => 1];
        $fakeRoles = collect(['admin', 'editor']);

        $this->userService
            ->shouldReceive('getUserWithRoles')
            ->with(1)
            ->once()
            ->andReturn($fakeUser);

        $this->roleService
            ->shouldReceive('getAllRoles')
            ->once()
            ->andReturn($fakeRoles);

        $request = Request::create('/', 'GET', ['id' => 1]);

        $response = $this->controller->EditUsersRole($request);

        $this->assertEquals('admin.edit_roles_form', $response->name());
        $this->assertEquals($fakeUser, $response->getData()['user']);
        $this->assertEquals($fakeRoles, $response->getData()['roles']);
    }
    public function test_revoke_user_redirects_with_success_message()
    {
        $this->userService
            ->shouldReceive('revokeUser')
            ->with(1)
            ->once();

        $request = Request::create('/', 'POST', ['id' => 1]);

        $response = $this->controller->revokeUser($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('admin.users'), $response->getTargetUrl());
        $this->assertEquals('Utilisateur retiré avec succès.', session('success'));
    }

    public function test_update_user_role_redirects_with_success_message()
    {
        $payload = ['id' => 1, 'roles' => [2, 3]];
        $this->userService
            ->shouldReceive('updateUserRole')
            ->with([
                'user_id' => 1,
                'role_ids' => [2, 3]
            ])
            ->once();

        $request = Request::create('/', 'POST', $payload);
        $response = $this->controller->updateUserRole($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('admin.users'), $response->getTargetUrl());
        $this->assertEquals('Rôle utilisateur mis à jour avec succès.', session('success'));
    }
    public function test_role_list_returns_view()
    {
        $response = $this->controller->RoleList();

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('admin.role_list', $response->getName());
    }
    public function test_permission_list_returns_view()
    {
        $response = $this->controller->PermissionList();

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('admin.permission_list', $response->getName());
    }
    public function test_user_requests_returns_view()
    {
        $response = $this->controller->UserRequests();

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('admin.user_request_list', $response->getName());
    }
    public function test_search_user_redirects_if_no_results()
    {
        $this->userService
            ->shouldReceive('searchUsers')
            ->with('nope')
            ->once()
            ->andReturn(collect());

        $request = Request::create('/', 'GET', ['query' => 'nope']);
        $response = $this->controller->searchUser($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('admin.users'), $response->getTargetUrl());
        $this->assertEquals('Aucun résultat trouvé', session('error'));
    }

    public function test_search_user_returns_view_with_results()
    {
        $fakeResults = collect(['foo', 'bar']);
        $this->userService
            ->shouldReceive('searchUsers')
            ->with('test')
            ->once()
            ->andReturn($fakeResults);

        $request = Request::create('/', 'GET', ['query' => 'test']);
        $response = $this->controller->searchUser($request);

        $this->assertEquals('admin.search_users_results', $response->name());
        $this->assertEquals($fakeResults, $response->getData()['users']);
        $this->assertEquals('test', $response->getData()['query']);
    }
}
