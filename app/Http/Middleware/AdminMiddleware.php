<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        $autorizedRoles =
        [
            'admin',
            'superadmin',

        ];
        if (!$user || !in_array($user->role, $autorizedRoles)) {
            return redirect()->back()->with(403, 'Vous n\'avez pas les droits pour accéder à cette page.');
        }
        return $next($request);
    }
}
