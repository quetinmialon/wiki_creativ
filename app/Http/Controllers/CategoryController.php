<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    public function index(){
        // Fetch all categories
        $categories = Category::all();
        return view('category.category-list',compact('categories'));
    }

    public function create(){
        // Fetch all roles for dropdown selection in the form
        $roles = Role::all();
        // Show form to create a new category
        return view('category.create-category-form',compact('roles'));
    }

    public function store(Request $request){
        // Validate the request data before creating a new category
        $request->validate([
            'name' =>'required|string|max:255',
            'role_id' =>'exists:roles,id|required'
        ]);
        // Create a new category
        Category::create($request->all());
        return redirect()->route('categories.index');
    }

    public function edit($id){
        $category = Category::find($id);
        // Fetch all roles for dropdown selection in the form
        $roles = Role::all();
        // Show form to edit the category
        return view('category.edit-category-form',compact('category','roles'));
    }

    public function update(Request $request, $id){
        // Validate the request data before updating the category
        $request->validate([
            'name' =>'required|string|max:255',
            'role_id' =>'exists:roles,id|required'
        ]);
        // Update the category
        $category = Category::find($id);
        $category->update($request->all());
        return redirect()->route('categories.index');
    }

    public function destroy($id){
     // Delete the category
        $category = Category::find($id);
        $category->delete();
        return redirect()->route('categories.index');
    }

    public function getUserCategories(){
        // Check if the user is authenticated and has a valid role_id
        if (!Auth::check() || Auth::user()->roles->isEmpty()) {
            return redirect()->route('login')->withErrors(['error' => 'Vous devez être connecté pour accéder à cette page.']);
        }
        // Fetch all categories associated with a user
        $categories = Category::whereIn('role_id', Auth::user()->roles->pluck('id'))->get();
        return view('category.user-categories-list', compact('categories'));
    }

    public function createCategoryOnUserRoles(){
        $user = Auth::user();
        if(!Auth::check() || Auth::user()->roles->isEmpty()){
            return redirect()->route('login')->withErrors(['error' => 'Vous devez être connecté pour accéder à cette page.']);
        }
        // Fetch all roles for dropdown selection in the form
        $roles = $user->roles->all();
        // Show form to create a new category
        return view('category.users-create-category-form',compact('roles'));
    }

    public function storeCategoryOnUserRoles(Request $request){
        $user = Auth::user();
        // Validate the request data before creating a new category
        $request->validate([
            'name' =>'required|string|max:255',
            'role_id' =>'exists:roles,id|required'
        ]);
        if(!$user->roles->contains('id', $request->role_id)){
            return redirect()->back()->withErrors(['role_id' => 'Vous ne pouvez pas créer une catégorie pour un rôle qui ne vous est pas attribué']);
        }
        // Create a new category
        Category::create($request->all());
        return redirect()->route('myCategories.myCategories');
    }

    public function destroyCategoryOnUserRoles($categoryId){

        $category = Category::find($categoryId);
        if(!Gate::allows('manage-category', $category)){
            abort(403);
        }
        $category->delete();
        return redirect()->route('myCategories.myCategories');
    }
    public function editCategoryOnUserRoles($categoryId){
        $user = Auth::user();
        $category = Category::find($categoryId);
        if(!Gate::allows('manage-category', $category)){
            abort(403);
        }

        // Fetch all roles for dropdown selection in the form
        $roles = $user->roles->all();
        // Show form to edit the category
        return view('category.users-edit-category-form',compact('category','roles'));
    }
    public function updateCategoryOnUserRoles(Request $request, $categoryId){
        $user = Auth::user();
        $category = Category::find($categoryId);
        if(!Gate::allows('manage-category', $category)){
            abort(403);
        }
        // Validate the request data before updating the category
        $request->validate([
            'name' =>'required|string|max:255',
            'role_id' =>'exists:roles,id|required'
        ]);
        $category->update($request->all());
        return redirect()->route('myCategories.myCategories');
    }
}
