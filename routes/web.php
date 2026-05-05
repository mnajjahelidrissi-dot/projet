<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DossierController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Auth\PasswordController; // ✅ Assurez-vous que cette ligne existe
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Routes Publiques
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

/*
|--------------------------------------------------------------------------
| Routes Authentifiées
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // ✅ Déconnexion
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');

    // ✅ Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::match(['put', 'patch'], '/password', [PasswordController::class, 'update'])->name('password.update');

    // --- Clients ---
    Route::prefix('clients')->name('clients.')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('index');
        Route::get('/creer', [ClientController::class, 'creer'])->name('creer');
        Route::post('/', [ClientController::class, 'enregistrer'])->name('enregistrer');
        Route::get('/{client}', [ClientController::class, 'afficher'])->name('afficher');
        Route::get('/{client}/modifier', [ClientController::class, 'modifier'])->name('modifier');
        Route::put('/{client}', [ClientController::class, 'mettreAJour'])->name('mettre-a-jour');
        Route::delete('/{client}', [ClientController::class, 'supprimer'])
            ->name('supprimer')
            ->middleware('verifier.role:administrateur,responsable');
    });

    // --- Dossiers ---
    Route::prefix('dossiers')->name('dossiers.')->group(function () {
        Route::get('/', [DossierController::class, 'index'])->name('index');
        Route::get('/creer', [DossierController::class, 'creer'])->name('creer');
        Route::post('/', [DossierController::class, 'enregistrer'])->name('enregistrer');
        Route::get('/{dossier}', [DossierController::class, 'afficher'])->name('afficher');
        Route::get('/{dossier}/modifier', [DossierController::class, 'modifier'])->name('modifier');
        Route::put('/{dossier}', [DossierController::class, 'mettreAJour'])->name('mettre-a-jour');
        Route::delete('/{dossier}', [DossierController::class, 'supprimer'])->name('supprimer');
    });

    // --- NOUVEAU : Profil & Password (REQUIS pour corriger l'erreur 405) ---

    // Le test PasswordUpdateTest envoie une requête PUT sur /password
    // On utilise la méthode 'update' du PasswordController

    // Routes pour le profil (souvent requises par les tests liés à Auth)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Administration ---
    Route::prefix('utilisateurs')->name('utilisateurs.')->middleware('verifier.role:administrateur')->group(function () {
        Route::get('/', [UtilisateurController::class, 'index'])->name('index');
        Route::get('/creer', [UtilisateurController::class, 'create'])->name('creer');
        Route::post('/', [UtilisateurController::class, 'store'])->name('enregistrer');
        Route::get('/{user}/modifier', [UtilisateurController::class, 'edit'])->name('modifier');
        Route::put('/{user}', [UtilisateurController::class, 'update'])->name('mettre-a-jour');
    });
});
