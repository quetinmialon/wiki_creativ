<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()){
            abort(401); // not connected
        }
        $user = Auth::user();
        $roles = $user->roles()->pluck('name')->toArray();
        if (!in_array('admin', $roles) &&!in_array('superadmin', $roles)){
            abort(403); // Accès interdit
        }

        return $next($request);
    }
}
