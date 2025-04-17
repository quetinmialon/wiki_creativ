<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use Mockery;
use App\Models\Category;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Services\RoleService;
use App\Models\User;
use App\Http\Controllers\CategoryController;
use App\Services\CategoryService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class CategoryControllerTest extends TestCase
{
    protected $categoryService;
    protected $roleService;
    protected $authService;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryService = Mockery::mock(CategoryService::class);
        $this->roleService = Mockery::mock(RoleService::class);
        $this->authService = Mockery::mock(AuthService::class);

        $this->controller = new CategoryController(
            $this->categoryService,
            $this->roleService,
            $this->authService
        );
    }

    public function test_index_displays_categories_view()
    {
        $categories = collect([new Category(['name' => 'Test'])]);
        $this->categoryService->shouldReceive('getAll')->once()->andReturn($categories);

        $response = $this->controller->index();

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('category.category-list', $response->name());
        $this->assertArrayHasKey('categories', $response->getData());
        $this->assertSame($categories, $response->getData()['categories']);
    }

    public function test_create_displays_create_form()
    {
        $roles = ['admin', 'user'];
        $this->roleService->shouldReceive('getAllRoles')->once()->andReturn($roles);

        $response = $this->controller->create();

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('category.create-category-form', $response->name());
        $this->assertArrayHasKey('roles', $response->getData());
        $this->assertSame($roles, $response->getData()['roles']);
    }

    public function test_store_redirects_after_creation()
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('validate')->andReturn([
            'name' => 'New Category',
            'role_id' => 1
        ]);

        $this->categoryService
            ->shouldReceive('create')
            ->once()
            ->with([
                'name' => 'New Category',
                'role_id' => 1
            ])
            ->andReturn(new Category(['name' => 'New Category', 'role_id' => 1]));

        $response = $this->controller->store($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(route('categories.index'), $response->headers->get('Location'));
    }


    public function test_edit_displays_edit_form()
    {
        $category = new Category(['name' => 'Cat']);
        $category->id = 1;
        $roles = ['admin'];

        $this->categoryService->shouldReceive('getById')->with(1)->once()->andReturn($category);
        $this->roleService->shouldReceive('getAllRoles')->once()->andReturn($roles);

        $response = $this->controller->edit(1);

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('category.edit-category-form', $response->name());
        $this->assertEquals($category, $response->getData()['category']);
        $this->assertEquals($roles, $response->getData()['roles']);
    }

    public function test_update_redirects_after_update()
    {
        $category = new Category();
        $category->id = 1;


        $this->categoryService
            ->shouldReceive('getById')
            ->with(1)
            ->once()
            ->andReturn($category);

            $this->categoryService
            ->shouldReceive('update')
            ->once()
            ->with(
                Mockery::on(fn($arg) => $arg instanceof Category && $arg->id === 1),
                [
                    'name' => 'Updated Category',
                    'role_id' => 2
                ]
            )
            ->andReturn(true);


        $request = Mockery::mock(Request::class);
        $request->shouldReceive('validate')->andReturn([
            'name' => 'Updated Category',
            'role_id' => 2
        ]);
        $response = $this->controller->update($request, $category->id);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(route('categories.index'), $response->headers->get('Location'));

    }

    public function test_destroy_redirects_after_deletion()
    {
        $category = new Category();
        $category->id = 1;
        $this->categoryService->shouldReceive('getById')
            ->with(1)
            ->once()
            ->andReturn($category);
        $this->categoryService->shouldReceive('delete')
        ->with(
            Mockery::on(fn($arg) => $arg instanceof Category && $arg->id === 1))
        ->once()
        ->andReturn(true);
        $response = $this->controller->destroy($category->id);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(route('categories.index'), $response->headers->get('Location'));
    }

    public function test_store_category_on_user_roles_redirects_after_creation()
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('validate')->andReturn([
            'name' => 'User Category',
            'role_id' => 2
        ]);

        $user = Mockery::mock(User::class);
        $user->shouldReceive('roles->contains')->with('id', 2)->andReturn(true);

        $this->authService->shouldReceive('getCurrentUser')->andReturn($user);

        $this->categoryService
            ->shouldReceive('createWithUserRole')
            ->once()
            ->with($user, [
                'name' => 'User Category',
                'role_id' => 2
            ])
            ->andReturn(new Category(['name' => 'User Category', 'role_id' => 2]));

        $response = $this->controller->storeCategoryOnUserRoles($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(route('myCategories.myCategories'), $response->headers->get('Location'));
    }
    public function test_updateCategoryOnUserRoles_redirects_after_update()
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('validate')->andReturn([
            'name' => 'Updated Role Category',
            'role_id' => 3
        ]);

        $user = Mockery::mock(User::class);
        $this->authService->shouldReceive('getCurrentUser')->andReturn($user);

        $category = Mockery::mock(Category::class);
        $this->categoryService->shouldReceive('getById')->with(5)->andReturn($category);

        Gate::shouldReceive('allows')->with('manage-category', $category)->andReturn(true);

        $this->categoryService
            ->shouldReceive('updateWithUserRole')
            ->once()
            ->with($user, $category,[
                'name' => 'Updated Role Category',
                'role_id' => 3
            ])
            ->andReturn(true);

        $response = $this->controller->updateCategoryOnUserRoles($request, 5);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(route('myCategories.myCategories'), $response->headers->get('Location'));
    }
}
