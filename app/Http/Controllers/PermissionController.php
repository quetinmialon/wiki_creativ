<?php

namespace App\Http\Controllers;

use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function index()
    {
        $permissions = $this->permissionService->getAllPermissions();
        return view('permission.permission-list', compact('permissions'));
    }

    public function pendingPermissions()
    {
        $permissions = $this->permissionService->getPendingPermissions();
        return view('permission.pending-permission-list', compact('permissions'));
    }

    public function createRequest(Request $request)
    {
        $request->validate([
            'document_id' => 'exists:documents,id',
            'expired_at' => 'required|date',
            'comment' => 'nullable|string'
        ]);

        $this->permissionService->createPermissionRequest($request->all());

        return redirect()->route('documents.allDocumentsInfo')->with('success', 'Demande de permission créée avec succès.');
    }

    public function requestForm($documentId)
    {
        $document = $this->permissionService->getDocumentById($documentId);
        if (!$document) {
            return redirect()->route('documents.index')->with('error', 'Document introuvable');
        }
        return view('permission.request-form', ['document' => $document]);
    }

    public function handleRequest(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,denied',
        ]);

        $permission = $this->permissionService->handlePermissionRequest($id, $request->status);
        if (!$permission) {
            return redirect()->route('admin.permissions.pendings')->with('error', 'Demande de permission introuvable.');
        }

        return redirect()->route('admin.permissions.pendings')->with('success', 'Demande de permission modifiée avec succès.');
    }

    public function destroy($id)
    {
        $permission = $this->permissionService->deletePermission($id);
        if (!$permission) {
            return redirect()->route('admin.permissions.pendings')->with('error', 'Demande de permission introuvable.');
        }

        return redirect()->route('admin.permissions.pendings')->with('success', 'Demande de permission supprimée avec succès.');
    }

    public function searchPermission(Request $request)
    {
        $query = $request->input('query');
        $permissions = $this->permissionService->searchPermissions($query);
        if ($permissions->isEmpty()) {
            return redirect()->route('admin.permissions')->with('error', 'Aucune permission trouvée.');
        }
        return view('permission.permission-list', compact('permissions', 'query'));
    }
}
