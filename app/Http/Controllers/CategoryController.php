<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;

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
            'name' =>'required',
            'role_id' =>'required'
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
            'name' =>'required',
            'role_id' =>'required'
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
        // Fetch all categories associated with a user
        $categories = Category::where('role_id',User::auth()->role_id)->get();
        return view('category.category-list',compact('categories'));
    }
}
