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
            'nom' => 'AGENT', 'prenom' => 'Test',
            'email' => 'agent@test.ma',
            'password' => Hash::make('Admin@123'),
            'role' => 'agent', 'actif' => true,
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

    private function creerClient(Utilisateur $agent): Client
    {
        return Client::create([
            'nom' => 'ALAMI', 'prenom' => 'Karim',
            'cin' => 'ZZ111111', 'telephone' => '0611111111',
            'statut' => 'actif', 'cree_par' => $agent->id,
        ]);
    }

    public function test_la_liste_des_dossiers_s_affiche(): void
    {
        $response = $this->actingAs($this->creerAgent())->get('/dossiers');
        $response->assertStatus(200);
        $response->assertSee('Liste des dossiers');
    }

    public function test_un_agent_peut_creer_un_dossier(): void
    {
        $agent  = $this->creerAgent();
        $client = $this->creerClient($agent);

        $response = $this->actingAs($agent)->post('/dossiers', [
            'client_id'    => $client->id,
            'type_demande' => 'ouverture_compte',
            'description'  => 'Test.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('succes');
        $this->assertDatabaseHas('dossiers', ['client_id' => $client->id, 'statut' => 'en_attente']);
        $this->assertDatabaseHas('historiques', ['action' => 'Dossier créé']);
    }

    public function test_la_creation_echoue_sans_client(): void
    {
        $response = $this->actingAs($this->creerAgent())->post('/dossiers', [
            'client_id' => '', 'type_demande' => 'reclamation',
        ]);

        $response->assertSessionHasErrors('client_id');
        $this->assertDatabaseCount('dossiers', 0);
    }

    public function test_un_agent_peut_changer_le_statut(): void
    {
        $agent  = $this->creerAgent();
        $client = $this->creerClient($agent);

        $dossier = Dossier::create([
            'client_id' => $client->id, 'type_demande' => 'demande_carte',
            'statut' => 'en_attente', 'cree_par' => $agent->id,
        ]);

        $response = $this->actingAs($agent)->post('/dossiers/' . $dossier->id . '/statut', [
            'statut' => 'en_cours', 'commentaire' => 'Pris en charge.',
        ]);

        $response->assertSessionHas('succes');
        $this->assertDatabaseHas('dossiers', ['id' => $dossier->id, 'statut' => 'en_cours']);
    }

    public function test_un_responsable_peut_affecter_un_agent(): void
    {
        $agent       = $this->creerAgent();
        $responsable = $this->creerResponsable();
        $client      = $this->creerClient($agent);

        $dossier = Dossier::create([
            'client_id' => $client->id, 'type_demande' => 'reclamation',
            'statut' => 'en_attente', 'cree_par' => $agent->id,
        ]);

        $response = $this->actingAs($responsable)->post('/dossiers/' . $dossier->id . '/agent', [
            'agent_id' => $agent->id,
        ]);

        $response->assertSessionHas('succes');
        $this->assertDatabaseHas('dossiers', ['id' => $dossier->id, 'agent_id' => $agent->id]);
    }

    public function test_un_agent_ne_peut_pas_affecter_un_agent(): void
    {
        $agent  = $this->creerAgent();
        $client = $this->creerClient($agent);

        $dossier = Dossier::create([
            'client_id' => $client->id, 'type_demande' => 'reclamation',
            'statut' => 'en_attente', 'cree_par' => $agent->id,
        ]);

        $response = $this->actingAs($agent)->post('/dossiers/' . $dossier->id . '/agent', [
            'agent_id' => $agent->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_le_numero_dossier_est_genere_automatiquement(): void
    {
        $agent  = $this->creerAgent();
        $client = $this->creerClient($agent);

        $dossier = Dossier::create([
            'client_id' => $client->id, 'type_demande' => 'ouverture_compte',
            'statut' => 'en_attente', 'cree_par' => $agent->id,
        ]);

        $this->assertMatchesRegularExpression('/^DOS-\d{4}-\d{4}$/', $dossier->numero_dossier);
    }
}
