<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use App\Models\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;

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
        // Log::info("⏱️ Token last used at: " . ($token->last_used_at ?? $token->created_at));
        // Log::info("⏳ Inactivity duration: {$inactiveSince} min");

        // Vérifier l'inactivité
        $inactiveSince = now()->diffInMinutes($token->last_used_at ?? $token->created_at);
        if ($inactiveSince >= 15) {
            $token->delete();
            return response()->json(['message' => 'Session expirée pour inactivité'], Response::HTTP_UNAUTHORIZED);
        }

        // Mettre à jour la dernière utilisation
        $token->forceFill(['last_used_at' => now()])->save();

        // Charger manuellement l'utilisateur connecté
        $user = $token->tokenable;

        $request->setUserResolver(fn() => $user);
        Auth::setUser($user); // pour faire fonctionner Auth::user()

        return $next($request);
    }
}
