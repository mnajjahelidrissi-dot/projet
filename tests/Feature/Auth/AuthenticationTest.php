<?php

namespace Tests\Feature\Auth;

use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

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

    /**
     * Un utilisateur actif peut se connecter avec ses identifiants corrects.
     */
    public function test_un_utilisateur_peut_se_connecter(): void
    {
        $utilisateur = $this->creerUtilisateur();

        $response = $this->postJson('/api/login', [
            'email'        => 'test@sahambank.ma',
            'password'     => 'motdepasse123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'token', 'user']);
        $this->assertAuthenticatedAs($utilisateur);
    }

    /**
     * Un utilisateur avec un mauvais mot de passe ne peut pas se connecter.
     */
    public function test_un_mauvais_mot_de_passe_est_refuse(): void
    {
        $this->creerUtilisateur();

        $response = $this->postJson('/api/login', [
            'email'        => 'test@sahambank.ma',
            'password'     => 'mauvais_mot_de_passe',
        ]);

        $this->assertGuest();
        $response->assertStatus(401); // 401 Unauthorized géré par ton AuthController
        $response->assertJson(['success' => false]);
    }

    /**
     * Un compte désactivé (actif = false) ne peut pas se connecter.
     */
    public function test_un_compte_desactive_est_refuse(): void
    {
        $this->creerUtilisateur(['actif' => false]);

        $response = $this->postJson('/api/login', [
            'email'        => 'test@sahambank.ma',
            'password'     => 'motdepasse123',
        ]);

        $this->assertGuest();
        $response->assertStatus(401);
    }

    /**
     * Un email inexistant est refusé.
     */
    public function test_un_email_inexistant_est_refuse(): void
    {
        $response = $this->postJson('/api/login', [
            'email'        => 'inconnu@sahambank.ma',
            'password'     => 'motdepasse123',
        ]);

        $this->assertGuest();
        $response->assertStatus(401);
    }

    /**
     * Un utilisateur connecté peut se déconcerter via l'API Sanctum.
     */
    public function test_un_utilisateur_peut_se_deconnecter(): void
    {
        $utilisateur = $this->creerUtilisateur();

        // Appel authentifié avec le guard sanctum
        $response = $this->actingAs($utilisateur, 'sanctum')->postJson('/api/logout');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /**
     * Un utilisateur non connecté reçoit un refus d'accès API (401).
     */
    public function test_une_page_protegee_renvoie_un_statut_non_authentifie(): void
    {
        $response = $this->getJson('/api/dashboard');
        $response->assertStatus(401);
    }

    /**
     * Un utilisateur connecté accède aux données du tableau de bord.
     */
    public function test_un_utilisateur_connecte_accede_au_tableau_de_bord(): void
    {
        $utilisateur = $this->creerUtilisateur();

        $response = $this->actingAs($utilisateur, 'sanctum')->getJson('/api/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'stats', 'derniersDossiers']);
    }
}
