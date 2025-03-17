<?php

namespace App\Http\Controllers;

use App\Rules\ValidMarkdown;
use App\Services\DocumentService;
use App\Services\FavoriteService;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\DocumentOpened;
use App\Models\Document;

class DocumentController extends Controller
{
    protected $documentService;
    protected $favoriteService;
    protected $logService;

    public function __construct(DocumentService $documentService, FavoriteService $favoriteService, LogService $logService)
    {
        $this->documentService = $documentService;
        $this->favoriteService = $favoriteService;
        $this->logService = $logService;
    }

    public function index()
    {
        $categories = $this->documentService->getAllCategoriesWithDocuments();
        return view('documents.document-list', compact('categories'));
    }

    public function create()
    {
        $user = Auth::user();
        $roles = $user->roles()->with('categories')->get();

        return view('documents.create-form', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'string|required',
            'content' => ['string', 'required', 'min:10', 'max:500000', new ValidMarkdown()],
            'excerpt' => 'string|nullable',
            'categories_id' => 'array|nullable',
            'categories_id.*' => 'exists:categories,id',
        ]);
        $this->documentService->createDocument($request->all());

        return redirect()->route('documents.index')->with('success', 'Créé avec succès');
    }


    public function byCategory($categoryId)
    {
        $documents = $this->documentService->getDocumentsByCategory($categoryId);
        return view('documents.by-category', compact('documents'));
    }


    public function show($id)
    {
        $document = Document::findOrFail($id);
        $userId = Auth::user()->id; // Récupère l'utilisateur connecté

        event(new DocumentOpened($document->id, $userId));

        return view('documents.document', compact('document'));
    }


    public function edit($id)
    {
        $document = $this->documentService->findDocument($id);
        $roles = $document->author->roles()->with('categories')->get();
        return view('documents.edit-form', compact('document', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'=> 'string|required',
            'content'=> ['string', 'required', 'min:10', 'max:500000', new ValidMarkdown()],
            'excerpt'=> 'string',
            'categories_id' => 'array',
            'categories_id.*'=> 'exists:categories,id',
        ]);

        $document = $this->documentService->findDocument($id)->first();
        $this->documentService->updateDocument($document, $request->all());

        return redirect()->route('documents.show', $document->id)->with('success', 'Modifié avec succès');
    }

    public function destroy($id)
    {
        $document = $this->documentService->findDocument($id)->first();
        $this->documentService->deleteDocument($document);

        return redirect()->route('documents.index')->with('success', 'Supprimé avec succès');
    }

    public function addToFavorite($documentId)
    {
        if ($this->favoriteService->addToFavorites($documentId)) {
            return redirect()->back()->with('success', 'Ajouté aux favoris.');
        }
        return redirect()->back()->with('error', 'Document introuvable.');
    }

    public function favorites()
    {
        $favorites = $this->favoriteService->getUserFavorites();
        return view('documents.favorites', compact('favorites'));
    }

    public function removeFromFavorite($documentId)
    {
        $response = $this->favoriteService->removeFromFavorites($documentId);
        if(!$response){
            return redirect()->back()->with('error', 'Document introuvable.');
        }
        return redirect()->back()->with('success', 'Retiré des favoris.');
    }

    public function logs($documentId)
    {
        $document = $this->logService->getDocumentLogs($documentId);

        if (!$document) {
            return redirect()->route('documents.index')->with('error', 'Document introuvable');
        }

        return view('documents.logs', [
            'document' => $document,
            'logs' => $document->logs
        ]);
    }

    public function everyLogs()
    {
        $logs = $this->logService->getAllLogs();
        return view('documents.all-logs', compact('logs'));
    }

    public function userLogs($userId)
    {
        $user = $this->logService->getUserLogs($userId);
        if (!$user) {
            return redirect()->route('documents.index')->with('error', 'Utilisateur introuvable');
        }
        return view('documents.user-logs', [
            'user' => $user,
            'logs' => $user->logs
        ]);
    }

    public function lastOpenedDocuments()
    {
        $logs = $this->logService->getLastOpenedDocuments();
        return view('documents.last-opened', compact('logs'));
    }
}
