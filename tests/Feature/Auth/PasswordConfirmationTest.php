<?php

namespace Tests\Feature\Auth;

use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    private function creerUtilisateur(): Utilisateur
    {
        return Utilisateur::create([
            'nom'      => 'TEST',
            'prenom'   => 'Confirm',
            'email'    => 'confirm@test.ma',
            'password' => Hash::make('Admin@123'),
            'role'     => 'agent',
            'actif'    => true,
        ]);
    }

    public function test_un_utilisateur_non_connecte_est_refuse_au_tableau_de_bord(): void
    {
        $response = $this->getJson('/api/dashboard');
        $response->assertStatus(401);
    }

    public function test_un_utilisateur_connecte_recoit_ses_donnees(): void
    {
        $utilisateur = $this->creerUtilisateur();

        $response = $this->actingAs($utilisateur, 'sanctum')->getJson('/api/dashboard');
        $response->assertStatus(200);
    }

    public function test_connexion_echoue_avec_un_mot_de_passe_invalide(): void
    {
        $this->creerUtilisateur();

        $response = $this->postJson('/api/login', [
            'email'    => 'confirm@test.ma',
            'password' => 'mauvais',
        ]);

        $this->assertGuest();
        $response->assertStatus(401);
    }
}
