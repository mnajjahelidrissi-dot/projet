<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DossierController;
use App\Http\Controllers\Api\DemandeController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\UtilisateurController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PasswordController;



/*
|--------------------------------------------------------------------------
| Routes Publiques (Accessibles sans authentification)
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);


/*
|--------------------------------------------------------------------------
| Routes Protégées par Jeton (Middleware Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    /**
     * Gestion du Profil et authentification
     */
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/password', [PasswordController::class, 'update']);

    /**
     *  Gestion du Profil de l'utilisateur connecté
     */
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::match(['put', 'patch'], '/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);

    /**
     * Dashboard
     */
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/admin', [DashboardController::class, 'adminDashboard'])
        ->middleware('role:administrateur,responsable');
    Route::get('/dashboard/agent', [DashboardController::class, 'agentDashboard'])
        ->middleware('role:agent');


    Route::get('/dashboard/export', [DashboardController::class, 'exportStats']);  // ← CORRIGÉ

    /**
     *   Clients
     */
    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index']);
        Route::post('/', [ClientController::class, 'store']);
        Route::get('/{client}', [ClientController::class, 'show']);
        Route::match(['put', 'patch'], '/{client}', [ClientController::class, 'update']);
        Route::delete('/{client}', [ClientController::class, 'destroy'])
            ->middleware('role:administrateur,responsable');
        Route::get('/export', [ClientController::class, 'export']);  // ← CORRIGÉ
    });

    /**
     *  Dossiers
     */
    Route::prefix('dossiers')->group(function () {
        Route::get('/', [DossierController::class, 'index']);
        Route::post('/', [DossierController::class, 'store']);
        Route::get('/{dossier}', [DossierController::class, 'show']);
        Route::match(['put', 'patch'], '/{dossier}', [DossierController::class, 'update']);
        Route::delete('/{dossier}', [DossierController::class, 'destroy']);
        Route::get('/{dossier}/historique', [DossierController::class, 'historique']);
        Route::get('/{dossier}/pdf', [DossierController::class, 'exportPdf']);
        Route::post('/{dossier}/affecter-agent', [DossierController::class, 'affecterAgent']);
        Route::patch('/{dossier}/statut', [DossierController::class, 'updateStatut'])
            ->middleware('role:responsable,administrateur');
        Route::get('/export', [DossierController::class, 'export']);  // ← AJOUTÉ
    });

    /**
     * Demandes
     */
    Route::prefix('demandes')->group(function () {
        Route::get('/', [DemandeController::class, 'index']);
        Route::post('/', [DemandeController::class, 'store']);
        Route::get('/{demande}', [DemandeController::class, 'show']);
        Route::delete('/{demande}', [DemandeController::class, 'destroy']);
    });

    /**
     *  Documents
     */
    Route::prefix('documents')->group(function () {
        Route::get('/', [DocumentController::class, 'index']);
        Route::post('/', [DocumentController::class, 'store']);
        Route::get('/{document}/download', [DocumentController::class, 'download']);
        Route::delete('/{document}', [DocumentController::class, 'destroy']);
    });

    /**
     *  Administration Utilisateurs
     */
    Route::prefix('utilisateurs')->middleware('role:administrateur')->group(function () {
        Route::get('/', [UtilisateurController::class, 'index']);
        Route::post('/', [UtilisateurController::class, 'store']);
        Route::get('/{utilisateur}', [UtilisateurController::class, 'show']);
        Route::match(['put', 'patch'], '/{utilisateur}', [UtilisateurController::class, 'update']);
        Route::post('/{utilisateur}/toggle-status', [UtilisateurController::class, 'toggleStatus']);
    });
});
