<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifierRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Logique de vérification (exemple)
        if (!auth()->check() || auth()->user()->role !== $role) {
            abort(403, "Action non autorisée.");
        }

        return $next($request);
    }
}
