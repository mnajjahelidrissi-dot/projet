<?php

namespace Tests\Feature;

use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function creerUtilisateur(): Utilisateur
    {
        return Utilisateur::create([
            'nom'      => 'TEST',
            'prenom'   => 'Profile',
            'email'    => 'profile@test.ma',
            'password' => Hash::make('Password123'),
            'role'     => 'agent',
            'actif'    => true,
        ]);
    }

    public function test_profile_page_is_displayed(): void
    {
        $user = $this->creerUtilisateur();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = $this->creerUtilisateur();

        $response = $this->actingAs($user)->patch('/profile', [
            'nom' => 'NOUVEAU_NOM',
            'prenom' => 'NouveauPrenom',
            'email' => 'nouveauprofile@test.ma',
            'telephone' => '0612345678',
        ]);

        $response->assertRedirect();

        $user->refresh();
        $this->assertSame('NOUVEAU_NOM', $user->nom);
        $this->assertSame('NouveauPrenom', $user->prenom);
        $this->assertSame('nouveauprofile@test.ma', $user->email);
    }

    public function test_email_cannot_be_duplicate(): void
    {
        $user1 = $this->creerUtilisateur();

        $user2 = Utilisateur::create([
            'nom' => 'AUTRE',
            'prenom' => 'Autre',
            'email' => 'autreprofile@test.ma',
            'password' => Hash::make('Password123'),
            'role' => 'agent',
            'actif' => true,
        ]);

        $response = $this->actingAs($user1)->patch('/profile', [
            'nom' => 'TEST',
            'prenom' => 'Profile',
            'email' => 'autreprofile@test.ma',
            'telephone' => '0612345678',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = $this->creerUtilisateur();

        $response = $this->actingAs($user)->delete('/profile', [
            'password' => 'Password123',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseMissing('utilisateurs', ['id' => $user->id]);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = $this->creerUtilisateur();

        $response = $this->actingAs($user)->delete('/profile', [
            'password' => 'MauvaisMotDePasse',
        ]);

        // Vérifie qu'il y a une erreur (sans spécifier le champ exact)
        $response->assertSessionHasErrors();
        $this->assertDatabaseHas('utilisateurs', ['id' => $user->id]);
    }
}
