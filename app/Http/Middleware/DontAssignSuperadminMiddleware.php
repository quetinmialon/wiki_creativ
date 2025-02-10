<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DontAssignSuperadminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user ) {
            return redirect()->route('login');
        }
        if ($request->role == 'superadmin') {
            return redirect()->back()->with(423, 'Vous ne pouvez pas affecter ce rôle.');
        }
        return $next($request);
    }
}
