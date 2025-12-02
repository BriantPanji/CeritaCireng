<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // In Laravel, middleware parameters can come as variadic args
        // For example: checkrole:dev,admin could be parsed as ['dev', 'admin'] 
        // OR as a single string 'dev,admin' depending on Laravel version

        // Log for debugging
        // \Log::info('CheckRole Middleware Debug', [
        //     'roles_received' => $roles,
        //     'count' => count($roles),
        //     'type' => gettype($roles),
        // ]);

        // If we receive multiple arguments (Laravel parsed the comma), use as-is
        // If we receive one argument, explode it
        if (count($roles) === 1 && is_string($roles[0])) {
            $rolesArray = explode(',', $roles[0]);
        } else {
            $rolesArray = $roles;
        }

        // Trim whitespace from each role
        $rolesArray = array_map('trim', $rolesArray);

        // Check for null/empty
        if (empty($rolesArray)) {
            abort(403, 'Role not specified.');
        }

        if (!Auth::check()) {
            abort(403, 'Unauthorized.');
        }

        // Check for wildcard
        if (in_array('*', $rolesArray) || in_array('all', $rolesArray)) {
            return $next($request);
        }

        // Check if user's role is in allowed roles
        if (!in_array(Auth::user()->role->name, $rolesArray)) {
            abort(403, 'Anda tidak memiliki Akses kesini!');
        }
        
        return $next($request);
    }
}
