<?php

namespace Tests\Feature;

use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function creerAdmin(): Utilisateur
    {
        return Utilisateur::create([
            'nom'      => 'ADMIN',
            'prenom'   => 'Test',
            'email'    => 'admin@test.ma',
            'password' => Hash::make('Admin@123'),
            'role'     => 'administrateur',
            'actif'    => true,
        ]);
    }

    private function creerAgent(): Utilisateur
    {
        return Utilisateur::create([
            'nom'      => 'AGENT',
            'prenom'   => 'Test',
            'email'    => 'agent@test.ma',
            'password' => Hash::make('Admin@123'),
            'role'     => 'agent',
            'actif'    => true,
        ]);
    }
/*
    public function test_profile_page_is_displayed(): void
    {
        $admin = $this->creerAdmin();

        $response = $this->actingAs($admin)->get('/utilisateurs');
        $response->assertStatus(200);
        $response->assertSee('Utilisateurs');
    }

    public function test_profile_information_can_be_updated(): void
    {
        $admin = $this->creerAdmin();
        $agent = $this->creerAgent();

        $response = $this->actingAs($admin)->put('/utilisateurs/' . $agent->id(), [
            'nom'    => 'MODIFIE',
            'prenom' => 'Prenom',
            'email'  => 'agent@test.ma',
            'role'   => 'agent',
            'actif'  => true,
        ]);

        $response->assertSessionHas('succes');
        $this->assertDatabaseHas('utilisateurs', ['nom' => 'MODIFIE']);
    }

    public function test_user_can_delete_their_account(): void
    {
        $admin = $this->creerAdmin();
        $agent = $this->creerAgent();

        $response = $this->actingAs($admin)->post('/utilisateurs/' . $agent->id() . '/statut');

        $response->assertSessionHas('succes');
        $this->assertDatabaseHas('utilisateurs', ['id' => $agent->id(), 'actif' => false]);
    }*/
}
