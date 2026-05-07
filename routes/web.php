<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DossierController;
use App\Http\Controllers\DemandeController;
use App\Http\Controllers\DashboardController;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::match(['put', 'patch'], '/password', [PasswordController::class, 'update'])->name('password.update');

    // --- Clients ---
    Route::prefix('clients')->name('clients.')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('index');
        Route::get('/creer', [ClientController::class, 'creer'])->name('creer');
        Route::post('/', [ClientController::class, 'enregistrer'])->name('enregistrer');
        Route::get('/{client}', [ClientController::class, 'afficher'])->name('show');
        Route::get('/{client}/modifier', [ClientController::class, 'modifier'])->name('modifier');
        Route::put('/{client}', [ClientController::class, 'mettreAJour'])->name('mettre-a-jour');
        Route::delete('/{client}', [ClientController::class, 'supprimer'])
            ->name('supprimer')
            ->middleware('role:administrateur,responsable');
    });

    // --- Dossiers ---
    Route::prefix('dossiers')->name('dossiers.')->group(function () {
        Route::get('/', [DossierController::class, 'index'])->name('index');
        Route::get('/creer', [DossierController::class, 'create'])->name('creer');        // ← create (pas creer)
        Route::post('/', [DossierController::class, 'store'])->name('enregistrer');       // ← store (pas enregistrer)
        Route::get('/{dossier}', [DossierController::class, 'show'])->name('show');   // ← show (pas afficher)
        Route::get('/{dossier}/modifier', [DossierController::class, 'edit'])->name('modifier');  // ← edit (pas modifier)
        Route::patch('/{dossier}/statut', [DossierController::class, 'updateStatut'])->name('update-statut');
        Route::put('/{dossier}', [DossierController::class, 'update'])->name('mettre-a-jour');    // ← update (pas mettreAJour)
        Route::delete('/{dossier}', [DossierController::class, 'destroy'])->name('supprimer');

        // ✅ Affectation d'un agent (nouvelle méthode)
        Route::post('/{dossier}/affecter-agent', [DossierController::class, 'affecterAgent'])->name('affecter-agent');

        // ✅ Historique du dossier (optionnel)
        Route::get('/{dossier}/historique', [DossierController::class, 'historique'])->name('historique');
    });

    // --- Demandes ---
Route::prefix('demandes')->name('demandes.')->group(function () {
    Route::get('/', [DemandeController::class, 'index'])->name('index');
    Route::get('/create', [DemandeController::class, 'create'])->name('create');
    Route::post('/', [DemandeController::class, 'store'])->name('store');
    Route::get('/{demande}', [DemandeController::class, 'show'])->name('show');
    Route::delete('/{demande}', [DemandeController::class, 'destroy'])->name('destroy');
});
    // ---// --- Documents ---
Route::prefix('documents')->name('documents.')->group(function () {
    Route::get('/', [DocumentController::class, 'index'])->name('index');
    Route::post('/', [DocumentController::class, 'store'])->name('store');
    Route::get('/{document}/download', [DocumentController::class, 'download'])->name('download');
    Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
});

    // Le test PasswordUpdateTest envoie une requête PUT sur /password
    // On utilise la méthode 'update' du PasswordController

    // Routes pour le profil (souvent requises par les tests liés à Auth)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Administration ---
   Route::prefix('utilisateurs')->name('utilisateurs.')->middleware('role:administrateur')->group(function () {
    Route::get('/', [UtilisateurController::class, 'index'])->name('index');
    Route::get('/creer', [UtilisateurController::class, 'create'])->name('creer');
    Route::post('/', [UtilisateurController::class, 'store'])->name('enregistrer');
    Route::get('/{utilisateur}/modifier', [UtilisateurController::class, 'edit'])->name('modifier');
    Route::put('/{utilisateur}', [UtilisateurController::class, 'update'])->name('mettre-a-jour');
    Route::post('/{utilisateur}/statut', [UtilisateurController::class, 'toggleStatus'])->name('basculer-status');
});
       // Exports
    Route::get('/export/clients', [ClientController::class, 'export'])->name('export.clients');
    Route::get('/export/dossiers', [DossierController::class, 'export'])->name('export.dossiers');
    Route::get('/export/stats', [DashboardController::class, 'exportStats'])->name('export.stats');


});
Route::get('/test-csrf', function () {
    return '
    <form method="POST" action="/test-csrf-post">
        <input type="hidden" name="_token" value="' . csrf_token() . '">
        <button type="submit">Tester CSRF</button>
    </form>
    <p>Token: ' . csrf_token() . '</p>
    ';
});

Route::post('/test-csrf-post', function () {
    return 'SUCCÈS ! Le formulaire fonctionne.';
});
