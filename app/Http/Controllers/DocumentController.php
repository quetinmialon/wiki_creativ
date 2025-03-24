<?php

namespace App\Http\Controllers;

use App\Rules\ValidMarkdown;
use App\Services\DocumentService;

use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Events\DocumentOpened;
use App\Models\Document;

class DocumentController extends Controller
{
    protected $documentService;
    protected $favoriteService;
    protected $logService;

    public function __construct(DocumentService $documentService, LogService $logService)
    {
        $this->documentService = $documentService;
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
        if ($user->cannot('create', Document::class)) {
            abort(403);
        }
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
        if(Auth::user()->cannot('view', $document)){
            abort(403);
        }
        event(new DocumentOpened($document->id, $userId));

        return view('documents.document', compact('document'));
    }


    public function edit($id)
    {
        $user = Auth::user();
        if ($user->cannot('update', $this->documentService->findDocument($id))) {
            abort(403);
        }
        $document = $this->documentService->findDocument($id);
        $roles = $document->author->roles()->with('categories')->get();
        return view('documents.edit-form', compact('document', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $document = $this->documentService->findDocument($id)->first();
        if (!Gate::allows('update-document', $document)) {
            abort(403, "Vous n'avez pas l'autorisation de modifier ce document.");
        }

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
        $user = Auth::user();
        if ($user->cannot('delete', $this->documentService->findDocument($id))) {
            abort(403);
        }
        $document = $this->documentService->findDocument($id)->first();
        $this->documentService->deleteDocument($document);

        return redirect()->route('documents.index')->with('success', 'Supprimé avec succès');
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
    public function getAllDocuments(){
        $categories = $this->documentService->getEveryDocuments();
        return view('documents.all-documents', compact('categories'));
    }
}
