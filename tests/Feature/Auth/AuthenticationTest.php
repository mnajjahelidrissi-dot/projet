<?php

namespace Tests\Feature\Auth;

use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tests d'authentification adaptés au modèle Utilisateur de Saham Bank.
 *
 * Pourquoi ces tests remplacent les tests Breeze par défaut ?
 * - Le modèle s'appelle Utilisateur (pas User)
 * - Le champ mot de passe s'appelle mot_de_passe (pas password)
 * - La route de connexion s'appelle 'login' (POST /connexion)
 * - La route après connexion est 'tableau-de-bord'
 * - Il n'y a pas de page d'inscription publique (ajout via admin uniquement)
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ────────────────────────────────────────────────

    /**
     * Crée un utilisateur de test valide.
     */
    private function creerUtilisateur(array $surcharges = []): Utilisateur
    {
        return Utilisateur::create(array_merge([
            'nom'          => 'TEST',
            'prenom'       => 'Utilisateur',
            'email'        => 'test@sahambank.ma',
            'password'     => Hash::make('motdepasse123'),
            'role'         => 'agent',
            'actif'        => true,
        ], $surcharges));
    }

    // ── Tests ──────────────────────────────────────────────────

    /**
     * La page de connexion s'affiche correctement.
     */
    public function test_la_page_de_connexion_s_affiche(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('SAHAM BANK');
        $response->assertSee('Se connecter');
    }

    /**
     * Un utilisateur actif peut se connecter avec ses identifiants corrects.
     */
    public function test_un_utilisateur_peut_se_connecter(): void
    {
        $utilisateur = $this->creerUtilisateur();

        $response = $this->post('/login', [
            'email'        => 'test@sahambank.ma',
            'password'     => 'motdepasse123',
        ]);

        // Vérifie la redirection vers le tableau de bord
        $response->assertRedirect(route('dashboard'));

        // Vérifie que l'utilisateur est bien authentifié
        $this->assertAuthenticatedAs($utilisateur);
    }

    /**
     * Un utilisateur avec un mauvais mot de passe ne peut pas se connecter.
     */
    public function test_un_mauvais_mot_de_passe_est_refuse(): void
    {
        $this->creerUtilisateur();

        $response = $this->post('/login', [
            'email'        => 'test@sahambank.ma',
            'password'     => 'mauvais_mot_de_passe',
        ]);

        // L'utilisateur reste non connecté
        $this->assertGuest();

        // Un message d'erreur est présent
        $response->assertSessionHasErrors('email');
    }

    /**
     * Un compte désactivé (actif = false) ne peut pas se connecter.
     */
    public function test_un_compte_desactive_est_refuse(): void
    {
        $this->creerUtilisateur(['actif' => false]);

        $response = $this->post('/login', [
            'email'        => 'test@sahambank.ma',
            'password'     => 'motdepasse123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /**
     * Un email inexistant est refusé.
     */
    public function test_un_email_inexistant_est_refuse(): void
    {
        $response = $this->post('/login', [
            'email'        => 'inconnu@sahambank.ma',
            'password'     => 'motdepasse123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /**
     * Un utilisateur connecté peut se déconnecter.
     */
    public function test_un_utilisateur_peut_se_deconnecter(): void
    {
        $utilisateur = $this->creerUtilisateur();

        // Se connecter d'abord
        $this->actingAs($utilisateur);
        $this->assertAuthenticatedAs($utilisateur);

        // Se déconnecter
        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    /**
     * Un utilisateur non connecté est redirigé vers la connexion.
     */
    public function test_une_page_protegee_redirige_vers_connexion(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Un utilisateur connecté accède au tableau de bord.
     */
    public function test_un_utilisateur_connecte_accede_au_tableau_de_bord(): void
    {
        $utilisateur = $this->creerUtilisateur();

        $response = $this->actingAs($utilisateur)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Bienvenue sur votre tableau de bord');
    }
}
