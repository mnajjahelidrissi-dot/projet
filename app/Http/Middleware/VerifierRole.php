<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifierRole
{
    /**
     * Gère la filtration des requêtes entrantes par rôle.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // 1. Si l'utilisateur n'est pas authentifié (Token invalide ou absent)
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié. Veuillez fournir un jeton d\'accès valide.'
            ], 401); // Code 401 Unauthorized
        }

        // 2. Si l'utilisateur est connecté mais n'a pas le bon rôle
        if (!in_array($user->role, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Accès interdit. Vous ne possédez pas les autorisations nécessaires pour effectuer cette action.'
            ], 403); // Code 403 Forbidden
        }

        return $next($request);
    }
}
