<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
// use Illuminate\Support\Facades\Log; // Remove if you added it for debugging and don't need it anymore

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure there's an authenticated user
        if ($request->user()) {
            // FIX: Access the 'value' property of the App\Enums\Role object
            if ($request->user()->role->value === 'admin') {
                return $next($request);
            }
        }

        // If no user or not an admin, return 403 Forbidden
        return response()->json(['message' => 'Unauthorized. Admins only.'], 403);
    }
}
