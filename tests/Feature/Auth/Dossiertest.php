<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Dossier;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DossierTest extends TestCase
{
    use RefreshDatabase;

    private function creerAgent(): Utilisateur
    {
        return Utilisateur::create([
            'nom' => 'AGENT',
            'prenom' => 'Test',
            'email' => 'agent@test.ma',
            'password' => Hash::make('Admin@123'),
            'role' => 'agent',
            'actif' => true,
        ]);
    }

    private function creerResponsable(): Utilisateur
    {
        return Utilisateur::create([
            'nom' => 'RESP',
            'prenom' => 'Test',
            'email' => 'resp@test.ma',
            'password' => Hash::make('Admin@123'),
            'role' => 'responsable',
            'actif' => true,
        ]);
    }

    private function creerClient(Utilisateur $agent): Client
    {
        return Client::create([
            'nom' => 'ALAMI',
            'prenom' => 'Karim',
            'cin' => 'ZZ111111',
            'telephone' => '0611111111',
            'statut' => 'actif',
            'cree_par' => $agent->id,
        ]);
    }

    public function test_la_liste_des_dossiers_s_affiche(): void
    {
        $response = $this->actingAs($this->creerAgent(), 'sanctum')->getJson('/api/dossiers');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
    }

    public function test_un_agent_peut_creer_un_dossier(): void
    {
        $agent  = $this->creerAgent();
        $client = $this->creerClient($agent);

        $response = $this->actingAs($agent, 'sanctum')->postJson('/api/dossiers', [
            'client_id'   => $client->id,
            'titre'       => 'Ouverture de compte courant', // S'aligne avec la validation de ton contrôleur
            'description' => 'Test.',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('dossiers', ['client_id' => $client->id, 'statut' => 'en_attente']);
        $this->assertDatabaseHas('historiques', ['action' => 'création']); // Doit correspondre à la constante Historique::ACTION_CREATION
    }

    public function test_la_creation_echoue_sans_client(): void
    {
        $response = $this->actingAs($this->creerAgent(), 'sanctum')->postJson('/api/dossiers', [
            'client_id' => '',
            'titre' => 'Titre test',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('client_id');
        $this->assertDatabaseCount('dossiers', 0);
    }

    public function test_un_responsable_peut_changer_le_statut(): void
    {
        $agent  = $this->creerAgent();
        $responsable = $this->creerResponsable();
        $client = $this->creerClient($agent);

        $dossier = Dossier::create([
            'client_id' => $client->id,
            'titre' => 'Demande de carte',
            'statut' => 'en_attente',
            'ouvert_par' => $agent->id,
        ]);

        // Seul le responsable a accès à cette route d'après ton fichier api.php
        $response = $this->actingAs($responsable, 'sanctum')->patchJson('/api/dossiers/' . $dossier->id . '/statut', [
            'statut' => 'en_cours',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('dossiers', ['id' => $dossier->id, 'statut' => 'en_cours']);
    }

    public function test_un_responsable_peut_affecter_un_agent(): void
    {
        $agent       = $this->creerAgent();
        $responsable = $this->creerResponsable();
        $client      = $this->creerClient($agent);

        $dossier = Dossier::create([
            'client_id' => $client->id,
            'titre' => 'Réclamation de frais',
            'statut' => 'en_attente',
            'ouvert_par' => $agent->id,
        ]);

        $response = $this->actingAs($responsable, 'sanctum')->postJson('/api/dossiers/' . $dossier->id . '/affecter-agent', [
            'agent_id' => $agent->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('dossiers', ['id' => $dossier->id, 'agent_id' => $agent->id]);
    }

    public function test_un_agent_ne_peut_pas_affecter_un_agent(): void
    {
        $agent  = $this->creerAgent();
        $client = $this->creerClient($agent);

        $dossier = Dossier::create([
            'client_id' => $client->id,
            'titre' => 'Réclamation de frais',
            'statut' => 'en_attente',
            'ouvert_par' => $agent->id,
        ]);

        $response = $this->actingAs($agent, 'sanctum')->postJson('/api/dossiers/' . $dossier->id . '/affecter-agent', [
            'agent_id' => $agent->id,
        ]);

        $response->assertStatus(403); // Interdit par le contrôleur (rôles admin ou responsable requis)
    }

    public function test_le_numero_dossier_est_genere_automatiquement(): void
    {
        $agent  = $this->creerAgent();
        $client = $this->creerClient($agent);

        $dossier = Dossier::create([
            'client_id' => $client->id,
            'titre' => 'Ouverture compte',
            'statut' => 'en_attente',
            'ouvert_par' => $agent->id,
        ]);

        // S'aligne sur l'expression régulière de ton modèle corrigé (ex: 2026-DOS-00001)
        $this->assertMatchesRegularExpression('/^\d{4}-DOS-\d{5}$/', $dossier->numero_dossier);
    }
}
