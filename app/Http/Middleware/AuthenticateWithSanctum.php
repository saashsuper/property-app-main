<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class AuthenticateWithSanctum
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Parse the token (format: ID|TOKEN)
        $tokenParts = explode('|', $token, 2);
        
        if (count($tokenParts) !== 2) {
            return response()->json(['message' => 'Invalid token format.'], 401);
        }

        [$id, $plainTextToken] = $tokenParts;

        // Find the token in database
        $accessToken = PersonalAccessToken::find($id);

        if (!$accessToken) {
            return response()->json(['message' => 'Token not found.'], 401);
        }

        // Verify the token hash matches
        if (!hash_equals($accessToken->token, hash('sha256', $plainTextToken))) {
            return response()->json(['message' => 'Invalid token.'], 401);
        }

        // Set the authenticated user
        $request->setUserResolver(function () use ($accessToken) {
            return $accessToken->tokenable;
        });

        // Update last used timestamp
        $accessToken->forceFill(['last_used_at' => now()])->save();

        return $next($request);
    }
}

