<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Exports\ClientsExport;
use Maatwebsite\Excel\Facades\Excel;

class ClientController extends Controller
{
    /**
     * GET /api/clients
     * Liste des clients avec recherche et pagination.
     */
    public function index(Request $request)
    {
        $recherche = $request->input('recherche');

        $clients = Client::withCount('dossiers')
            ->with('createur')
            ->when($recherche, function ($query, $recherche) {
                $query->where('nom', 'like', "%{$recherche}%")
                    ->orWhere('prenom', 'like', "%{$recherche}%")
                    ->orWhere('cin', 'like', "%{$recherche}%")
                    ->orWhere('numero_client', 'like', "%{$recherche}%")
                    ->orWhere('telephone', 'like', "%{$recherche}%");
            })
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $clients
        ], 200);
    }

    /**
     * POST /api/clients
     * Enregistrement d'un nouveau client.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom'            => 'required|string|max:100',
            'prenom'         => 'required|string|max:100',
            'cin'            => 'required|string|max:20|unique:clients,cin',
            'date_naissance' => 'nullable|date|before:today',
            'telephone'      => 'required|string|max:20',
            'email'          => 'nullable|email|max:150',
            'adresse'        => 'nullable|string|max:255',
            'ville'          => 'nullable|string|max:100',
            'profession'     => 'nullable|string|max:100',
        ], [
            'nom.required'           => 'Le nom est obligatoire.',
            'prenom.required'        => 'Le prénom est obligatoire.',
            'cin.required'           => 'La CIN est obligatoire.',
            'cin.unique'             => 'Cette CIN est déjà enregistrée.',
            'telephone.required'     => 'Le téléphone est obligatoire.',
            'email.email'            => 'L\'email n\'est pas valide.',
            'date_naissance.before'  => 'La date de naissance doit être dans le passé.',
        ]);

        $client = Client::create([
            'nom'            => strtoupper($request->nom),
            'prenom'         => ucfirst(strtolower($request->prenom)),
            'cin'            => strtoupper($request->cin),
            'date_naissance' => $request->date_naissance,
            'telephone'      => $request->telephone,
            'email'          => $request->email,
            'adresse'        => $request->adresse,
            'ville'          => $request->ville,
            'profession'     => $request->profession,
            'statut'         => 'actif',
            'cree_par'       => $request->user()->id, // Récupération de l'ID via le Token API
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Client enregistré avec succès.',
            'data' => $client
        ], 201);
    }

    /**
     * GET /api/clients/{id}
     * Afficher le détail complet d'un client.
     */
    public function show(Client $client)
    {
        $client->load(['dossiers.agent', 'createur']);

        return response()->json([
            'success' => true,
            'data' => $client
        ], 200);
    }

    /**
     * PUT/PATCH /api/clients/{id}
     * Mise à jour des informations du client.
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'nom'            => 'required|string|max:100',
            'prenom'         => 'required|string|max:100',
            'cin'            => 'required|string|max:20|unique:clients,cin,' . $client->id,
            'date_naissance' => 'nullable|date|before:today',
            'telephone'      => 'required|string|max:20',
            'email'          => 'nullable|email|max:150',
            'adresse'        => 'nullable|string|max:255',
            'ville'          => 'nullable|string|max:100',
            'profession'     => 'nullable|string|max:100',
            'statut'         => 'required|in:actif,inactif',
        ], [
            'nom.required'       => 'Le nom est obligatoire.',
            'prenom.required'    => 'Le prénom est obligatoire.',
            'cin.required'       => 'La CIN est obligatoire.',
            'cin.unique'         => 'Cette CIN est déjà utilisée par un autre client.',
            'telephone.required' => 'Le téléphone est obligatoire.',
        ]);

        $client->update([
            'nom'            => strtoupper($request->nom),
            'prenom'         => ucfirst(strtolower($request->prenom)),
            'cin'            => strtoupper($request->cin),
            'date_naissance' => $request->date_naissance,
            'telephone'      => $request->telephone,
            'email'          => $request->email,
            'adresse'        => $request->adresse,
            'ville'          => $request->ville,
            'profession'     => $request->profession,
            'statut'         => $request->statut,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Client mis à jour avec succès.',
            'data' => $client
        ], 200);
    }

    /**
     * DELETE /api/clients/{id}
     * Suppression d'un client.
     */
    public function destroy(Client $client)
    {
        // Vérifier si le client possède des dossiers reliés
        if ($client->dossiers()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer ce client car il possède des dossiers.'
            ], 422); // 422 Unprocessable Entity (Erreur logique métier)
        }

        $client->delete();

        return response()->json([
            'success' => true,
            'message' => 'Client supprimé avec succès.'
        ], 200);
    }

    /**
     * GET /api/clients/export
     * Exporter la liste des clients en fichier Excel.
     */
    public function export(Request $request)
    {
        $filtres = [
            'recherche' => $request->input('recherche')
        ];

        return Excel::download(new ClientsExport($filtres), 'clients_' . date('Y-m-d') . '.xlsx');
    }
}
