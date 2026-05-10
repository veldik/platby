<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $key = config('app.api_key');

        if (!$key || $request->header('X-Api-Key') !== $key) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
