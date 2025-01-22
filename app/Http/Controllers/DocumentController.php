<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index()
    {
        // Charger toutes les catégories avec leurs documents et les auteurs des documents
        $categories = Category::with(['documents.author'])->get();

        return view('documents.document-list', compact('categories'));
    }



    public function create()
    {
        // Récupérer tous les rôles et leurs catégories associées
        $user = Auth::user();
        $roles = $user->roles()->with('categories')->get();

        return view('documents.create-form', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=> 'string|required',
            'content'=> 'string|required',
            'excerpt'=> 'string|required',
            'categories_id' => 'array',
            'categories_id.*'=> 'exists:categories,id',
        ]);

        $request['created_by'] = Auth::user()->id;

        $document = Document::create($request->all());

        if ($request->has('categories_id') && count($request->categories_id)) {
            $document->categories()->attach($request->categories_id);
        }
        return redirect()->route('documents.index')->with('success','créé avec succès');
    }

    public function byCategory($categoryId)
    {
        $category = Category::find($categoryId);
        if (!$category) {
            return redirect()->route('categories.index')->with('error', 'Catégorie introuvable');
        }
        $documents = $category->documents()->get();
        return view('documents.by-category', compact('category', 'documents'));
    }

    public function show($id)
    {
        $document = Document::find($id);
        if (!$document) {
            return redirect()->route('document.index')->with('error', 'Document introuvable');
        }
        return view('documents.document', compact('document'));
    }

    public function edit($id)
    {
        $document = Document::find($id);
        if (!$document) {
            return redirect()->route('documents.index')->with('error', 'Document introuvable');
        }
        $user = Auth::user();
        $roles = $user->roles()->with('categories')->get();
        return view('documents.edit-form', compact('document', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'=> 'string|required',
            'content'=> 'string|required',
            'excerpt'=> 'string|required',
            'categories_id' => 'array',
            'categories_id.*'=> 'exists:categories,id',
        ]);

        $document = Document::find($id);
        if (!$document) {
            return redirect()->route('documents.index')->with('error', 'Document introuvable');
        }
        $document->update($request->all());

        $document->categories()->sync($request->categories_id);

        return redirect()->route('documents.show', $document->id)->with('success','modifié avec succès');
    }

}
