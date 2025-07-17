<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use App\Models\PersonalAccessToken;


class AuthWithExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $accessToken = $request->bearerToken();

        if (!$accessToken || !Str::contains($accessToken, '|')) {
            return response()->json(['message' => 'Token invalide'], Response::HTTP_UNAUTHORIZED);
        }

        [$tokenId, $tokenValue] = explode('|', $accessToken, 2);

        $token = PersonalAccessToken::find($tokenId);

        if (!$token || !hash_equals($token->token, hash('sha256', $tokenValue))) {
            return response()->json(['message' => 'Token non reconnu'], Response::HTTP_UNAUTHORIZED);
        }

        // Vérifier l'inactivité
        $inactiveSince = now()->diffInMinutes($token->last_used_at ?? $token->created_at);
        Log::info("⏱️ Token last used at: " . ($token->last_used_at ?? $token->created_at));
        Log::info("⏳ Inactivity duration: {$inactiveSince} min");

        if ($inactiveSince >= 15) { // mettre à 15 en prod  
        $token->delete();
            return response()->json(['message' => 'Session expirée pour inactivité'], Response::HTTP_UNAUTHORIZED);
        }

        // Mettre à jour manuellement last_used_at
        $token->forceFill(['last_used_at' => now()])->save();

        // Charger l'utilisateur connecté
        $request->setUserResolver(function () use ($token) {
            return $token->tokenable;
        });

        return $next($request);
    }
}
