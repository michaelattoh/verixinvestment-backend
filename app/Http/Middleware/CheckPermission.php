<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        $user = $request->user();

        if (!$user || !$user->hasPermission($permission)) {
            return response()->json(['error' => 'Unauthorized. Missing permission: '.$permission], 403);
        }

        return $next($request);
    }
}
