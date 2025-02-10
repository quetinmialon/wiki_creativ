<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ActualUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        $autorizedRoles = [
            'admin',
               'superadmin',
        ];

        if (!$user || $user->id != $request->route('user')->id || !in_array($user->role, $autorizedRoles)) {
            return redirect()->back()->with('error',"vous n'avez pas le droit d'accès à cette page.");
        }

        return $next($request);
    }
}
