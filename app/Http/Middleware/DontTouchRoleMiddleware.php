<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DontTouchRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $coreRoles = [
            'admin',
            'superadmin',
            'default',
            'qualité',
        ];

        $user = Auth::user();
        if (!$user ) {
            return redirect()->route('login');
}

        if (in_array($request->role, $coreRoles)) {
            return redirect()->back()->with(423, 'Vous ne pouvez pas modifier ou supprimer ce rôle.');
        }

        return $next($request);
    }
}
