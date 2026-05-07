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

        // S'assurer qu'il y a un admin dans la base
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

    /*public function test_admin_peut_desactiver_un_utilisateur(): void
    {
        $admin = $this->creerAdmin();
        $agent = $this->creerAgent();

        // Vérifier que l'agent est actif au départ
        $this->assertTrue($agent->actif);

        // Admin essaie de désactiver l'agent
        $response = $this->actingAs($admin)->post('/utilisateurs/' . $agent->id . '/statut');

        // Recharger l'agent
        $agent->refresh();

        // Vérifier que l'agent est maintenant inactif
        $this->assertFalse($agent->actif);
    }*/
       public function test_admin_peut_desactiver_un_utilisateur(): void
{
    $admin = $this->creerAdmin();
    $agent = $this->creerAgent();

    $this->assertTrue($agent->actif);

    // SANS dd() ici
    $response = $this->actingAs($admin)->post('/utilisateurs/' . $agent->id . '/statut');

    $agent->refresh();

    $this->assertFalse($agent->actif);
}
    public function test_admin_peut_reactiver_un_utilisateur(): void
    {
        $admin = $this->creerAdmin();

        // Créer un agent inactif
        $agent = Utilisateur::create([
            'nom'      => 'AGENT',
            'prenom'   => 'Test',
            'email'    => 'agent_inactif_' . uniqid() . '@test.com',
            'password' => Hash::make('Agent123'),
            'role'     => 'agent',
            'actif'    => false,
        ]);

        // Vérifier que l'agent est inactif au départ
        $this->assertFalse($agent->actif);

        // Admin essaie de réactiver l'agent
        $response = $this->actingAs($admin)->post('/utilisateurs/' . $agent->id . '/statut');

        // Recharger l'agent
        $agent->refresh();

        // Vérifier que l'agent est maintenant actif
        $this->assertTrue($agent->actif);
    }

    public function test_un_agent_ne_peut_pas_desactiver_un_autre_utilisateur(): void
    {
        $agent1 = $this->creerAgent();
        $agent2 = $this->creerAgent();

        // Un agent tente de désactiver un autre agent
        $response = $this->actingAs($agent1)->post('/utilisateurs/' . $agent2->id . '/statut');

        // Vérifier la redirection (pas d'erreur 500)
        $this->assertTrue(in_array($response->status(), [302, 403]));

        // Vérifier que l'agent2 est toujours actif
        $agent2->refresh();
        $this->assertTrue($agent2->actif);
    }

    public function test_un_utilisateur_ne_peut_pas_se_desactiver_lui_meme(): void
    {
        $admin = $this->creerAdmin();

        // L'admin tente de se désactiver lui-même
        $response = $this->actingAs($admin)->post('/utilisateurs/' . $admin->id . '/statut');

        // Vérifier que l'admin est toujours actif
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

        $response = $this->post('/login', [
            'email' => $agent->email,
            'password' => 'Agent123',
        ]);

        $this->assertGuest();
    }
}
