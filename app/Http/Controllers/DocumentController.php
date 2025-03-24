<?php

namespace App\Http\Controllers;

use App\Rules\ValidMarkdown;
use App\Services\DocumentService;
use App\Services\RoleService;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Events\DocumentOpened;
use App\Models\Document;
use App\Models\Role;

class DocumentController extends Controller
{
    protected $documentService;
    protected $favoriteService;
    protected $logService;
    protected $roleService;

    public function __construct(DocumentService $documentService, LogService $logService, RoleService $roleService)
    {
        $this->documentService = $documentService;
        $this->logService = $logService;
        $this->roleService = $roleService;
    }

    public function index()
    {
        $categories = $this->documentService->getAllCategoriesWithDocuments();
        return view('documents.document-list', compact('categories'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user) {
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
        if (!Gate::allows('view-document', $document)) {
            abort(403);
        }
        event(new DocumentOpened($document->id, $userId));

        return view('documents.document', compact('document'));
    }


    public function edit($id)
    {
        $user = Auth::user();
        if(!Gate::allows('manage-document',$this->documentService->findDocument($id)) && !Gate::allows('is-superadmin') ){
            abort(403);
        }
        $document = $this->documentService->findDocument($id);
        $roles = $this->roleService->getRolesWhereCategoriesExist();
        return view('documents.edit-form', compact('document', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $document = $this->documentService->findDocument($id)->first();
        if(!Gate::allows('manage-document',$this->documentService->findDocument($id)->first()) && !Gate::allows('is-superadmin') ){
            abort(403);
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
        if(!Gate::allows('manage-document',$this->documentService->findDocument($id)->first()) && !Gate::allows('is-superadmin') ){
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
