<?php

use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;

test('les utilisateurs peuvent s\'authentifier via l\'écran de connexion', function () {
    // Correction : Utilisation du modèle Utilisateur avec un mot de passe connu
    $user = Utilisateur::factory()->create([
        'password' => Hash::make('password'),
        'actif' => true,
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();

    // Correction : Vos contrôleurs redirigent vers 'tableau-de-bord' après login
    $response->assertRedirect(route('tableau-de-bord'));
});

test('les utilisateurs ne peuvent pas s\'authentifier avec un mauvais mot de passe', function () {
    $user = Utilisateur::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();

    // Correction : AuthController utilise back() en cas d'erreur
    $response->assertSessionHasErrors('email');
});

test('les utilisateurs peuvent se déconnecter', function () {
    $user = Utilisateur::factory()->create();

    // Correction : AuthController redirige vers 'login' après logout[cite: 1]
    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect(route('login'));
});
