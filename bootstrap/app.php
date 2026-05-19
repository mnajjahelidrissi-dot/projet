<?php

use App\Http\Middleware\VerifierRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;  // Pour la communication entre React et Laravel

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ✅ 1. AJOUTER CORS AU GROUPE API
        $middleware->api(prepend: [
            HandleCors::class,
        ]);

        // ✅ 2. EXCLURE LES ROUTES API DU CSRF (IMPORTANT POUR REACT)
        $middleware->validateCsrfTokens(except: [
            'api/*',           // Toutes les routes API
            'api/login',       // Spécifiquement login
            'api/logout',      // Spécifiquement logout
        ]);

        // ✅ 3. CONFIGURATION DU GROUPE WEB
        $middleware->web(append: [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        ]);

        // ✅ 4. ALIAS DES MIDDLEWARES PERSONNALISÉS
        $middleware->alias([
            'role' => VerifierRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {})
    ->create();
