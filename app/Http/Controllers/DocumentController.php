<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Document;
use App\Models\Favorite;
use App\Models\Log;
use App\Models\User;
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
    public function destroy($id)
    {
        $document = Document::find($id);
        if (!$document) {
            return redirect()->route('documents.index')->with('error', 'Document introuvable');
        }
        $document->delete();
        return redirect()->route('documents.index')->with('success','supprimé avec succès');
    }

    public function addToFavorite($documentId)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('home')->with('error', 'Vous devez être connecté pour ajouter un document en favoris.');
        }

        $document = Document::find($documentId);
        if (!$document) {
            return redirect()->back()->with('error', 'Document introuvable.');
        }

        // Vérifie si le document est déjà en favori
        $alreadyFavorite = Favorite::where('user_id', $user->id)
                                   ->where('document_id', $documentId)
                                   ->exists();
        if (!$alreadyFavorite) {
            // Crée une nouvelle instance de Favorite
            Favorite::create([
                'user_id' => $user->id,
                'document_id' => $documentId,
            ]);
        }

        return redirect()->back()->with('success', 'Document ajouté à vos favoris avec succès.');
    }


    public function favorites()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('home')->with('error', 'Vous devez être connecté pour consulter vos documents favoris.');
        }

        // Récupérer les documents favoris de l'utilisateur
        $favorites = Document::whereIn('id', $user->favorites->pluck('document_id'))->get();

        return view('documents.favorites', compact('favorites'));
    }


    public function removeFromFavorite($documentId)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('home')->with('error', 'Vous devez être connecté pour retirer un document de vos favoris.');
        }

        $document = Document::find($documentId);
        if (!$document) {
            return redirect()->back()->with('error', 'Document introuvable.');
        }

        // Trouver l'entrée favorite correspondante et la supprimer
        $favorite = Favorite::where('user_id', $user->id)
                            ->where('document_id', $documentId)
                            ->first();

        if ($favorite) {
            $favorite->delete();
        }

        return redirect()->back()->with('success', 'Document retiré de vos favoris avec succès.');
    }


    public function logs($documentId)
    {
        $document = Document::find($documentId);
        if (!$document) {
            return redirect()->route('documents.index')->with('error', 'Document introuvable');
        }
        $logs = $document->logs;
        return view('documents.logs', compact('document', 'logs'));
    }

    public function addLog($documentId)
    {
        $document = Document::find($documentId);
        if (!$document) {
            return redirect()->route('documents.index')->with('error', 'Document introuvable');
        }
        $user = Auth::user();
        $log = new Log([
            'user_id' => $user->id,
            'document_id' => $document->id,
        ]);
        $log->save();
    }

    public function everyLogs()
    {
        $logs = Log::all();
        return view('documents.all-logs', compact('logs'));
    }

    public function userLogs($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('home')->with('error', 'Utilisateur introuvable');
        }
        $logs = $user->logs;
        return view('documents.user-logs', compact('user', 'logs'));
    }

    public function lastOpenedDocuments()
    {
        $userId = Auth::id();

        // Récupérer les 5 derniers logs avec les documents associés
        $logs = Log::with('document')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Passer les logs à la vue
        return view('documents.last-opened', compact('logs'));
    }

}
