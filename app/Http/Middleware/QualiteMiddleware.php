<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class QualiteMiddleware
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
            return redirect()->route('login')->with('error', "Connectez vous pour acceder à cette page");
        }
        $autorizedRoles =
        [
            'superadmin',
            'qualité',
            'Admin qualité'
        ];
        $roles = $user->roles()->pluck('name')->toArray();

        if (!$user || !array_intersect($roles, $autorizedRoles)) {
            return redirect()->back()->with('Vous n\'avez pas les droits pour accéder à cette page.');
        }
        return $next($request);
    }
}
