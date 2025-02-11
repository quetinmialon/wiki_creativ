<?php

namespace App\Http\Middleware;

use App\Models\Document;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class ReadDocumentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next):Response
    {
        $user = Auth::user()->loadRoles();
        if (!$user) {
            return redirect()->route('login');
        }

        $document = Document::with('categories.role')->findOrFail($request->DocumentId);
        $categoriesOfDocument = $document->categories;

        foreach ($user->roles as $role) {
            foreach ($categoriesOfDocument as $category) {
                if ($category->role && $category->role->id == $role->id)//TODO : maybe add also qualité, admin and superadmin roles an access to read documnts
                {
                    return $next($request);
                }
            }
        }
        return redirect()->back()->with(403, 'Accès interdit à ce document.');
    }
}
