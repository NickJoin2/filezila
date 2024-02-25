<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,...$abilities): Response
    {
        foreach ($abilities as $ability) {
            if($request->user()->tokenCan($ability)){
                return $next($request);
            } else {
                return response()->json([
                    'error' => [
                        'code' => 403,
                        'message' => 'Forbidden for you'
                    ]
                ], 403);
            }
        }

    }
}
