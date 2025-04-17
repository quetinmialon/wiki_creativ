<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\AuthService;
use App\Services\CategoryService;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    protected $categoryService;
    protected $roleService;
    protected $authService;

    public function __construct(CategoryService $categoryService, RoleService $roleService, AuthService $authService)
    {
        $this->categoryService = $categoryService;
        $this->roleService = $roleService;
        $this->authService = $authService;
    }

    public function index()
    {
        $categories = $this->categoryService->getAll();
        return view('category.category-list', compact('categories'));
    }

    public function create()
    {
        $roles = $this->roleService->getAllRoles();
        return view('category.create-category-form', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role_id' => 'exists:roles,id|required',
        ]);

        $this->categoryService->create($data);
        return redirect()->route('categories.index');
    }

    public function edit($id)
    {
        $category = $this->categoryService->getById($id);
        $roles = $this->roleService->getAllRoles();
        return view('category.edit-category-form', compact('category', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $category = $this->categoryService->getById($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role_id' => 'exists:roles,id|required',
        ]);

        $this->categoryService->update($category, $data);
        return redirect()->route('categories.index');
    }

    public function destroy($id)
    {
        $category = $this->categoryService->getById($id);
        $this->categoryService->delete($category);
        return redirect()->route('categories.index');
    }

    public function getUserCategories()
    {
        if (!$this->authService->isAuthenticated() || $this->authService->getCurrentUser()->roles->isEmpty()) {
            return redirect()->route('login')->withErrors(['error' => 'Vous devez être connecté pour accéder à cette page.']);
        }

        $user = $this->authService->getCurrentUser();
        $categories = $this->categoryService->getUserCategories($user);
        return view('category.user-categories-list', compact('categories'));
    }

    public function createCategoryOnUserRoles()
    {
        if (!$this->authService->isAuthenticated() || $this->authService->getCurrentUser()->roles->isEmpty()) {
            return redirect()->route('login')->withErrors(['error' => 'Vous devez être connecté pour accéder à cette page.']);
        }

        $user = $this->authService->getCurrentUser();
        $roles = $this->categoryService->getRolesForUser($user);
        return view('category.users-create-category-form', compact('roles'));
    }

    public function storeCategoryOnUserRoles(Request $request)
    {
        $user = $this->authService->getCurrentUser();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role_id' => 'exists:roles,id|required',
        ]);

        $this->categoryService->createWithUserRole($user, $data);
        return redirect()->route('myCategories.myCategories');
    }

    public function destroyCategoryOnUserRoles($categoryId)
    {
        $category = $this->categoryService->getById($categoryId);

        if (!Gate::allows('manage-category', $category)) {
            abort(403);
        }

        $this->categoryService->delete($category);
        return redirect()->route('myCategories.myCategories');
    }

    public function editCategoryOnUserRoles($categoryId)
    {
        $user = $this->authService->getCurrentUser();
        $category = $this->categoryService->getById($categoryId);

        if (!Gate::allows('manage-category', $category)) {
            abort(403);
        }

        $roles = $this->categoryService->getRolesForUser($user);
        return view('category.users-edit-category-form', compact('category', 'roles'));
    }

    public function updateCategoryOnUserRoles(Request $request, $categoryId)
    {
        $user = $this->authService->getCurrentUser();
        $category = $this->categoryService->getById($categoryId);

        if (!Gate::allows('manage-category', $category)) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role_id' => 'exists:roles,id|required',
        ]);

        $this->categoryService->updateWithUserRole($user, $category, $data);
        return redirect()->route('myCategories.myCategories');
    }
}
