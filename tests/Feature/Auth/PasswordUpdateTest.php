<?php

namespace Tests\Feature;

use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    private function creerUtilisateur(): Utilisateur
    {
        return Utilisateur::create([
            'nom'      => 'TEST',
            'prenom'   => 'Password',
            'email'    => 'password@test.ma',
            'password' => Hash::make('AncienMotDePasse123'),
            'role'     => 'agent',
            'actif'    => true,
        ]);
    }

    public function test_password_can_be_updated(): void
    {
        $user = $this->creerUtilisateur();

        $response = $this->actingAs($user, 'sanctum')->patchJson('/api/profile', [
            'nom' => 'TEST',
            'prenom' => 'Password',
            'email' => 'password@test.ma',
            'current_password' => 'AncienMotDePasse123',
            'password' => 'NouveauMotDePasse456',
            'password_confirmation' => 'NouveauMotDePasse456',
        ]);

        $response->assertStatus(200);
        $this->assertTrue(Hash::check('NouveauMotDePasse456', $user->fresh()->password));
    }

    public function test_correct_password_must_be_provided_to_update(): void
    {
        // Note : Ton ProfileUpdateRequest doit être configuré pour valider le current_password
        $user = $this->creerUtilisateur();

        $response = $this->actingAs($user, 'sanctum')->patchJson('/api/profile', [
            'nom' => 'TEST',
            'prenom' => 'Password',
            'email' => 'password@test.ma',
            'current_password' => 'MauvaisMotDePasse',
            'password' => 'NouveauMotDePasse456',
            'password_confirmation' => 'NouveauMotDePasse456',
        ]);

        $response->assertStatus(422);
    }

    public function test_new_password_must_be_confirmed(): void
    {
        $user = $this->creerUtilisateur();

        $response = $this->actingAs($user, 'sanctum')->patchJson('/api/profile', [
            'nom' => 'TEST',
            'prenom' => 'Password',
            'email' => 'password@test.ma',
            'current_password' => 'AncienMotDePasse123',
            'password' => 'NouveauMotDePasse456',
            'password_confirmation' => 'ConfirmationDifferente',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }
}
