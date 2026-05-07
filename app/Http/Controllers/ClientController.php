<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\ClientsExport;
use Maatwebsite\Excel\Facades\Excel;
class ClientController extends Controller
{
    // Liste des clients avec recherche
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

        return view('clients.index', compact('clients', 'recherche'));
    }

    // Formulaire de création
    public function creer()
    {
        return view('clients.creer');
    }

    // Enregistrement du nouveau client
    public function enregistrer(Request $request)
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
            'nom.required'       => 'Le nom est obligatoire.',
            'prenom.required'    => 'Le prénom est obligatoire.',
            'cin.required'       => 'La CIN est obligatoire.',
            'cin.unique'         => 'Cette CIN est déjà enregistrée.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'email.email'        => 'L\'email n\'est pas valide.',
            'date_naissance.before' => 'La date de naissance doit être dans le passé.',
        ]);

        Client::create([
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
            'cree_par'       => Auth::id(),
        ]);

        return redirect()->route('clients.index')->with('succes', 'Client enregistré avec succès.');
    }

    // Afficher le détail d'un client
    public function afficher(Client $client)
    {
        $client->load(['dossiers.agent', 'createur']);
        return view('clients.afficher', compact('client'));
    }

    // Formulaire de modification
    public function modifier(Client $client)
    {
        return view('clients.modifier', compact('client'));
    }

    // Mise à jour du client
    public function mettreAJour(Request $request, Client $client)
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

        return redirect()->route('clients.afficher', $client)->with('succes', 'Client mis à jour avec succès.');
    }

    // Suppression du client
    public function supprimer(Client $client)
    {
        // Vérifier qu'il n'a pas de dossiers
        if ($client->dossiers()->count() > 0) {
            return back()->with('erreur', 'Impossible de supprimer ce client car il possède des dossiers.');
        }

        $client->delete();

        return redirect()->route('clients.index')->with('succes', 'Client supprimé avec succès.');
    }
    public function export(Request $request)
{
    $filtres = [
        'recherche' => $request->input('recherche')
    ];

    return Excel::download(new ClientsExport($filtres), 'clients_' . date('Y-m-d') . '.xlsx');
}
}
