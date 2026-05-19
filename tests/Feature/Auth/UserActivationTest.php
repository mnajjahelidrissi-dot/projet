<?php

namespace Tests\Feature\Auth;

use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserActivationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (Utilisateur::where('role', 'administrateur')->count() === 0) {
            Utilisateur::create([
                'nom' => 'Admin',
                'prenom' => 'System',
                'email' => 'admin@system.com',
                'password' => Hash::make('password'),
                'role' => 'administrateur',
                'actif' => true,
            ]);
        }
    }

    private function creerAdmin(): Utilisateur
    {
        return Utilisateur::create([
            'nom'      => 'ADMIN',
            'prenom'   => 'Test',
            'email'    => 'admin_' . uniqid() . '@test.com',
            'password' => Hash::make('Admin123'),
            'role'     => 'administrateur',
            'actif'    => true,
        ]);
    }

    private function creerAgent(): Utilisateur
    {
        return Utilisateur::create([
            'nom'      => 'AGENT',
            'prenom'   => 'Test',
            'email'    => 'agent_' . uniqid() . '@test.com',
            'password' => Hash::make('Agent123'),
            'role'     => 'agent',
            'actif'    => true,
        ]);
    }

    public function test_admin_peut_desactiver_un_utilisateur(): void
    {
        $admin = $this->creerAdmin();
        $agent = $this->creerAgent();

        $this->assertTrue($agent->actif);

        // Correction de l'URL d'action selon ton fichier de routes : /api/utilisateurs/{id}/toggle-status
        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/utilisateurs/' . $agent->id . '/toggle-status');

        $response->assertStatus(200);
        $agent->refresh();
        $this->assertFalse($agent->actif);
    }

    public function test_admin_peut_reactiver_un_utilisateur(): void
    {
        $admin = $this->creerAdmin();

        $agent = Utilisateur::create([
            'nom'      => 'AGENT',
            'prenom'   => 'Test',
            'email'    => 'agent_inactif_' . uniqid() . '@test.com',
            'password' => Hash::make('Agent123'),
            'role'     => 'agent',
            'actif'    => false,
        ]);

        $this->assertFalse($agent->actif);

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/utilisateurs/' . $agent->id . '/toggle-status');

        $response->assertStatus(200);
        $agent->refresh();
        $this->assertTrue($agent->actif);
    }

    public function test_un_agent_ne_peut_pas_desactiver_un_autre_utilisateur(): void
    {
        $agent1 = $this->creerAgent();
        $agent2 = $this->creerAgent();

        $response = $this->actingAs($agent1, 'sanctum')->postJson('/api/utilisateurs/' . $agent2->id . '/toggle-status');

        $response->assertStatus(403); // L'accès de l'API renvoie un 403 Forbidden propre
        $agent2->refresh();
        $this->assertTrue($agent2->actif);
    }

    public function test_un_utilisateur_ne_peut_pas_se_desactiver_lui_meme(): void
    {
        $admin = $this->creerAdmin();

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/utilisateurs/' . $admin->id . '/toggle-status');

        $response->assertStatus(403);
        $admin->refresh();
        $this->assertTrue($admin->actif);
    }

    public function test_un_utilisateur_desactive_ne_peut_pas_se_connecter(): void
    {
        $agent = Utilisateur::create([
            'nom'      => 'AGENT',
            'prenom'   => 'Desactive',
            'email'    => 'desactive_' . uniqid() . '@test.com',
            'password' => Hash::make('Agent123'),
            'role'     => 'agent',
            'actif'    => false,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $agent->email,
            'password' => 'Agent123',
        ]);

        $this->assertGuest();
        $response->assertStatus(401);
    }
}
