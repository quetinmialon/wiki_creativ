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
use App\Models\Category;


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
        $validate = $request->validate([
            'name' => 'string|required','max:255',
            'content' => ['string', 'required', 'min:10', 'max:500000', new ValidMarkdown()],
            'excerpt' => 'string|nullable','max:255',
            'categories_id' => 'array|nullable',
            'categories_id.*' => 'exists:categories,id',
        ]);
        if (empty($request->categories_id)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['categories_id' => "Veuillez sélectionner au moins une catégorie. Si aucune ne correspond, vous pouvez en créer une adaptée <a href='" . route('myCategories.create') . "' class='underline text-blue-500'>ici</a>."]);
        }

        $this->documentService->createDocument($validate);

        return redirect()->route('documents.index')->with('success', 'Créé avec succès');
    }


    public function byCategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $documents = $this->documentService->getDocumentsByCategory($categoryId);
        return view('documents.by-category', compact('category', 'documents'));
    }



    public function show($id)
    {
        $document = $this->documentService->findDocument($id);

        if(!$document) {
            return redirect()->route('documents.index')->with('error', 'Document introuvable');
        }
        if(!$document->formated_name)
        {
            return redirect()->route('documents.index')->with('error', 'Document non disponnible');
        }
        $userId = Auth::user()->id; // Récupère l'utilisateur connecté
        if (!Gate::allows('view-document', $document) && !Gate::allows('access-document', $document)) {
            abort(403);
        }
        event(new DocumentOpened($document->id, $userId));

        return view('documents.document', compact('document'));
    }


    public function edit($id)
    {
        $document = $this->documentService->findDocument($id);
        $user = Auth::user();
        if (
            !Gate::forUser($user)->allows('manage-document', $document) &&
            !Gate::forUser($user)->allows('is-superadmin')
        ) {
            abort(403);
        }
        $roles = $this->roleService->getRolesWhereCategoriesExist();
        return view('documents.edit-form', compact('document', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $document = $this->documentService->findDocument($id);
        $user = Auth::user();
        if (
            !Gate::forUser($user)->allows('manage-document', $document) &&
            !Gate::forUser($user)->allows('is-superadmin')
        ) {
            abort(403);
        }
        $request->validate([
            'name' => 'string|required',
            'content' => ['string', 'required', 'min:10', 'max:500000', new ValidMarkdown()],
            'excerpt' => 'string',
            'categories_id' => 'array|required',
            'categories_id.*' => 'exists:categories,id|min:1',
        ]);
        if (empty($request->categories_id)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['categories_id' => "Veuillez sélectionner au moins une catégorie. Si aucune ne correspond, vous pouvez en créer une adaptée <a href='" . route('myCategories.create') . "' class='underline text-blue-500'>ici</a>."]);
        }

        $this->documentService->updateDocument($document, $request->all());

        return redirect()->route('documents.show', ['document'=>$document->id])->with('success', 'Modifié avec succès');
    }

    public function destroy($id)
    {
        $document = $this->documentService->findDocument($id);

        if (
            !Gate::allows('manage-document', $document) &&
            !Gate::allows('is-superadmin') &&
            $document->created_by !== Auth::id()
        ) {
            abort(403);
        }

        $this->documentService->deleteDocument($document);

        return redirect()
            ->route('documents.index')
            ->with('success', 'Supprimé avec succès');
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
        $categories = $this->documentService->getEveryDocumentswithoutPagination();

        return view('documents.all-documents', compact('categories'));
    }

    public function AllDocumentsInfo()
    {
        $categories = $this->documentService->getEveryDocumentswithoutPagination()->except('content');
        return view ('documents.all-documents-info', compact('categories'));
    }

    public function search(Request $request){
        $query = $request->input('query');
        $documents = $this->documentService->searchDocuments($query);
        if($documents->isEmpty()){
            return redirect()->route('documents.index')->with('error', 'Aucun résultat trouvé');
        }
        if($request->input('admin') == 'admin'){
            return view('documents.admin-search-results', compact('documents','query'));
        }
        return view('documents.search-results', compact('documents','query'));
    }
}
