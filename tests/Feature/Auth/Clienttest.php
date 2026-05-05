<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    private function agent(): Utilisateur
    {
        return Utilisateur::create([
            'nom' => 'AGENT', 'prenom' => 'Test',
            'email' => 'agent@test.ma',
            'password' => Hash::make('Admin@123'),
            'role' => 'agent', 'actif' => true,
        ]);
    }

    private function admin(): Utilisateur
    {
        return Utilisateur::create([
            'nom' => 'ADMIN', 'prenom' => 'Test',
            'email' => 'admin@test.ma',
            'password' => Hash::make('Admin@123'),
            'role' => 'administrateur', 'actif' => true,
        ]);
    }

    private function donnees(array $surcharges = []): array
    {
        return array_merge([
            'nom' => 'BENALI', 'prenom' => 'Sara',
            'cin' => 'AB999999', 'telephone' => '0661234567',
            'email' => 'sara@test.ma', 'ville' => 'Casablanca',
        ], $surcharges);
    }

    public function test_la_liste_des_clients_s_affiche(): void
    {
        $response = $this->actingAs($this->agent())->get('/clients');
        $response->assertStatus(200);
        $response->assertSee('Liste des clients');
    }

    public function test_un_agent_peut_creer_un_client(): void
    {
        $agent = $this->agent();
        $response = $this->actingAs($agent)->post('/clients', $this->donnees());

        $response->assertRedirect('/clients');
        $response->assertSessionHas('succes');
        $this->assertDatabaseHas('clients', ['cin' => 'AB999999']);
    }

    public function test_la_creation_echoue_sans_cin(): void
    {
        $response = $this->actingAs($this->agent())->post('/clients', $this->donnees(['cin' => '']));
        $response->assertSessionHasErrors('cin');
        $this->assertDatabaseCount('clients', 0);
    }

    public function test_la_creation_echoue_avec_cin_dupliquee(): void
    {
        $agent = $this->agent();
        $this->actingAs($agent)->post('/clients', $this->donnees(['cin' => 'DOUBLON1']));

        $response = $this->actingAs($agent)->post('/clients', $this->donnees([
            'cin' => 'DOUBLON1', 'email' => 'autre@test.ma',
        ]));

        $response->assertSessionHasErrors('cin');
        $this->assertDatabaseCount('clients', 1);
    }

    public function test_admin_peut_supprimer_un_client_sans_dossiers(): void
    {
        $admin  = $this->admin();
        $client = Client::create(array_merge($this->donnees(), ['cree_par' => $admin->id]));

        $response = $this->actingAs($admin)->delete('/clients/' . $client->id);

        $response->assertRedirect('/clients');
        $this->assertDatabaseMissing('clients', ['cin' => 'AB999999']);
    }

    public function test_agent_ne_peut_pas_supprimer_un_client(): void
    {
        $agent  = $this->agent();
        $client = Client::create(array_merge($this->donnees(), ['cree_par' => $agent->id]));

        $response = $this->actingAs($agent)->delete('/clients/' . $client->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('clients', ['cin' => 'AB999999']);
    }

    public function test_la_recherche_filtre_les_clients(): void
    {
        $agent = $this->agent();
        Client::create(array_merge($this->donnees(['nom' => 'MOUSSAOUI', 'cin' => 'AA000001']), ['cree_par' => $agent->id]));
        Client::create(array_merge($this->donnees(['nom' => 'TAZI',      'cin' => 'AA000002']), ['cree_par' => $agent->id]));

        $response = $this->actingAs($agent)->get('/clients?recherche=MOUSSAOUI');

        $response->assertStatus(200);
        $response->assertSee('MOUSSAOUI');
        $response->assertDontSee('TAZI');
    }
}
