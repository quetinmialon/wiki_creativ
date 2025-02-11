<?php

namespace App\Http\Middleware;

use App\Models\Document;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthorDocumentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $document = Document::findOrFail($request->documentId);
        if ($document->user_id != $user->id ) {
            return redirect()->back()->with(403, 'Accès à la modification de ce document interdite.');
        }
        return $next($request);
    }
}
