<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;

class PermissionService
{
    public function getAllPermissions()
    {
        return Permission::with('document')->get();
    }

    public function getPendingPermissions()
    {
        return Permission::with('document')->where('status', 'pending')->get();
    }

    public function createPermissionRequest(array $data)
    {
        return Permission::create([
            'document_id' => $data['document_id'],
            'expired_at' => $data['expired_at'],
            'comment' => $data['comment'] ?? null,
            'status' => 'pending',
            'author' => Auth::id(),
        ]);
    }

    public function getDocumentById($documentId)
    {
        return Document::find($documentId);
    }

    public function handlePermissionRequest($id, $status)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return null;
        }

        $permission->status = $status;
        $permission->handled_by = Auth::id();
        $permission->save();

        return $permission;
    }

    public function deletePermission($id)
    {
        $permission = Permission::find($id);
        if ($permission) {
            $permission->delete();
        }

        return $permission;
    }

    public function getUserPermissions($userId)
    {
        return Permission::with('document')->where('author', $userId)->get();
    }

    public function getDocumentPermissions($documentId)
    {
        return Permission::with('document')->where('document_id', $documentId)->get();
    }


    public function searchPermissions($query)
    {
        return Permission::with(['document', 'users'])
            ->whereHas('document', function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->orWhereHas('author', function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->orWhere('comment', 'LIKE', "%{$query}%")
            ->get();
    }

}
