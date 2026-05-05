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

    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_password_can_be_confirmed(): void
    {
        $utilisateur = $this->creerUtilisateur();

        $response = $this->actingAs($utilisateur)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $this->creerUtilisateur();

        $response = $this->post('/login', [
            'email'    => 'confirm@test.ma',
            'password' => 'mauvais',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }
}
