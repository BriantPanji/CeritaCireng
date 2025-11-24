<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
        if ($roles === null) {
            abort(403, 'Role not specified.');
        }

        if (!Auth::check()) {
            abort(403, 'Unauthorized.');
        }

        // if (Auth::user()->role->name !== $roles) {
        if ($roles === '*' || $roles === 'all') {
            return $next($request);
        }
        $allowed = array_map('trim', explode(',', $roles));
        if (!in_array(Auth::user()->role->name, $allowed)) {
            abort(403, 'Anda tidak memiliki Akses kesini!');
        }


        return $next($request);
    }
}
