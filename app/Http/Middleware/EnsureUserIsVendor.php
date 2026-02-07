<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsVendor
{
    /**
     * Handle an incoming request.
     *
     * Ensures the user is a vendor AND their vendor profile is active.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->type !== User::TYPE_VENDOR) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('Unauthorized. Vendor access required.'),
                ], 403);
            }

            return redirect()->route('login');
        }

        // Check vendor profile is active
        $vendor = $user->vendor;

        if (! $vendor || ! $vendor->is_active) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('Your vendor account is inactive. Please contact support.'),
                ], 403);
            }

            return redirect()->route('login')->with('error', 'Your vendor account is inactive.');
        }

        return $next($request);
    }
}
