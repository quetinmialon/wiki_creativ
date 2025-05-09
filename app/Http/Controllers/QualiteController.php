<?php

namespace App\Http\Controllers;

use App\Rules\ValidMarkdown;
use Illuminate\Http\Request;
use App\Services\DocumentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\RoleService;

class QualiteController extends Controller
{
    protected DocumentService $documentService;
    protected RoleService $roleService;

    public function __construct(DocumentService $documentService, RoleService $roleService)

    {
        $this->documentService = $documentService;
        $this->roleService = $roleService;
    }

    private function accessQualityPages()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', "Connectez vous pour acceder à cette page");
        }
        if (!Gate::allows('qualite', $user)) {
            return redirect()->route('home')->with('error', "Vous n'avez pas les droits d'accès à cette page");
        }
    }

    public function index()
    {
        if ($response = $this->accessQualityPages()) {
            return $response;
        }

        $document = $this->documentService->getAllDocumentThatAreNotNormed();
        return view('qualite.index', compact('document'));
    }

    public function addNormedNameToDocument(Request $request)
    {
        if ($response = $this->accessQualityPages()) {
            return $response;
        }
        $request->validate([
            'formated_name' => 'nullable|string|max:255',
            'id' => 'required|integer|exists:documents,id',
        ]);
        $document = $this->documentService->findDocument($request->id);
        if (!$document) {
            return redirect()->route('qualite.index')->with('error', "Le document n'existe pas en base de donnée");
        }
        if($request->formated_name == null) {
            $this->documentService->addNormedNameToDocument($document, null);
            return redirect()->route('qualite.index')->with('success', "nomenclature retirée avec succès, le document n'est plus accessible aux utilisateurs");
        }
        $this->documentService->addNormedNameToDocument($document, $request->formated_name);
        return redirect()->route('qualite.index')->with('success', 'Nomanclature ajouté avec succès, le document est accessible aux utilisateurs.');
    }
    public function edit(Request $request)
    {
        if ($response = $this->accessQualityPages()) {
            return $response;
        }
        $document = $this->documentService->findDocument($request->id);
        if (!$document) {
            return redirect()->route('qualite.index')->with('error', "Le document n'existe pas en base de donnée");
        }
        $roles = $this->roleService->getRolesWhereCategoriesExist();
        return view('qualite.edit_document', compact('document','roles'));
    }
    public function update(Request $request)
    {
        if ($response = $this->accessQualityPages()) {

            return $response;
        }
        $request->validate([
            'name' => 'string|required|max:255',
            'formated_name' => 'nullable|string|max:255',
            'id' => 'required|integer',
            'content' => 'required|string|max:5000000|min:10|', new ValidMarkdown(),
            'excerpt' => 'string|nullable|max:255',
            'categories_id' => 'array|nullable',
            'categories_id.*' => 'exists:categories,id',
        ]);
        if (empty($request->categories_id)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['categories_id' => "Veuillez sélectionner au moins une catégorie. Si aucune ne correspond, vous pouvez en créer une adaptée <a href='" . route('myCategories.create') . "' class='underline text-blue-500'>ici</a>."]);
        }
        $document = $this->documentService->findDocument($request->id);
        if (!$document) {
            return redirect()->route('qualite.index')->with('error', "Le document n'existe pas en base de donnée");
        }
        $this->documentService->updateDocument($document, $request->all());
        return redirect()->route('qualite.index')->with('success', 'Document mis à jour avec succès.');
    }

    public function documentList()
    {
        if ($response = $this->accessQualityPages()) {
            return $response;
        }
        $document = $this->documentService->getAllDocumentThatAreNormed();
        return view('qualite.document_list', compact('document'));
    }
}
