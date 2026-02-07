<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->type !== User::TYPE_ADMIN) {
            return response()->json([
                'message' => __('Unauthorized. Admin access required.'),
            ], 403);
        }

        return $next($request);
    }
}
