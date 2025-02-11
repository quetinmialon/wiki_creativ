<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::with('document')->get();
        return view('permission.permission-list', compact('permissions'));
    }

    public function pendingPermissions()
    {
        $permissions = Permission::with('document')->where('status', 'pending')->get();
        return view('permission.pending-permission-list', compact('permissions'));
    }

    public function createRequest(Request $request)
    {
        $request->validate([
            'document_id' => 'exists:documents,id',
            'expired_at' => 'required|date',
            'comment' => 'nullable|string'
        ]);

        Permission::create([
            'document_id' => $request->document_id,
            'expired_at' => $request->expired_at,
            'comment' => $request->comment,
            'status' => 'pending',
            'author' => Auth::id(),
        ]);

        return redirect()->route('pending-permissions')->with('success', 'Demande de permission créée avec succès.');
    }

    public function requestForm($documentId)
    {
        $document = Document::find($documentId);
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

        $permission = Permission::find($id);
        if (!$permission) {
            return redirect()->route('pending-permissions')->with('error', 'Demande de permission introuvable.');
        }

        // TODO: Envoyer une notification par email
        $permission->status = $request->status;
        $permission->handled_by = Auth::id();
        $permission->save();

        return redirect()->route('pending-permissions')->with('success', 'Demande de permission modifiée avec succès.');
    }

    public function destroy($id)
    {
        // TODO: Convertir cette méthode pour qu’elle soit gérée via un événement Chronus
        $permission = Permission::find($id);
        if (!$permission) {
            return redirect()->route('pending-permissions')->with('error', 'Demande de permission introuvable.');
        }
        $permission->delete();

        return redirect()->route('pending-permissions')->with('success', 'Demande de permission supprimée avec succès.');
    }

    public function cancelRequest($id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return redirect()->route('pending-permissions')->with('error', 'Demande de permission introuvable.');
        }

        if ($permission->status != 'pending') {
            return redirect()->route('pending-permissions')->with('error', 'Impossible d\'annuler une demande de permission déjà traitée.');
        }

        $permission->delete();
        return redirect()->route('pending-permissions')->with('success', 'Demande de permission annulée avec succès.');
    }

    public function userRequest($id)
    {
        $permissions = Permission::with('document')->where('author', $id)->get();
        return view('permission.user-request-list', compact('permissions'));
    }

    public function documentRequest($id)
    {
        $permissions = Permission::with('document')->where('document_id', $id)->get();
        return view('permission.document-request-list', compact('permissions'));
    }
}
