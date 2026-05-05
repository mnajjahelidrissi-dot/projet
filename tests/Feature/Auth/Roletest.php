<?php

namespace Tests\Feature\Auth;

use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    private function creerAdmin(): Utilisateur
    {
        return Utilisateur::create([
            'nom' => 'ADMIN', 'prenom' => 'Test',
            'email' => 'admin@test.ma',
            'password' => Hash::make('Admin@123'),
            'role' => 'administrateur', 'actif' => true,
        ]);
    }

    private function creerResponsable(): Utilisateur
    {
        return Utilisateur::create([
            'nom' => 'RESP', 'prenom' => 'Test',
            'email' => 'resp@test.ma',
            'password' => Hash::make('Admin@123'),
            'role' => 'responsable', 'actif' => true,
        ]);
    }

    private function creerAgent(): Utilisateur
    {
        return Utilisateur::create([
            'nom' => 'AGENT', 'prenom' => 'Test',
            'email' => 'agent@test.ma',
            'password' => Hash::make('Admin@123'),
            'role' => 'agent', 'actif' => true,
        ]);
    }

    public function test_administrateur_accede_aux_utilisateurs(): void
    {
        $response = $this->actingAs($this->creerAdmin())->get('/utilisateurs');
        $response->assertStatus(200);
    }

    public function test_responsable_ne_peut_pas_gerer_les_utilisateurs(): void
    {
        $response = $this->actingAs($this->creerResponsable())->get('/utilisateurs');
        $response->assertStatus(403);
    }

    public function test_agent_ne_peut_pas_gerer_les_utilisateurs(): void
    {
        $response = $this->actingAs($this->creerAgent())->get('/utilisateurs');
        $response->assertStatus(403);
    }

    public function test_agent_accede_a_la_liste_des_clients(): void
    {
        $response = $this->actingAs($this->creerAgent())->get('/clients');
        $response->assertStatus(200);
    }

    public function test_agent_accede_a_la_liste_des_dossiers(): void
    {
        $response = $this->actingAs($this->creerAgent())->get('/dossiers');
        $response->assertStatus(200);
    }
}
