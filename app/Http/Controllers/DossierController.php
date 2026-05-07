<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\Client;
use App\Models\Demande;
use App\Models\Utilisateur;
use App\Models\Historique;
use App\Notifications\DossierStatutNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\DossiersExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class DossierController extends Controller
{
    public function index(Request $request)
    {
        $query = Dossier::with(['client', 'agent']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }
        if ($request->filled('non_affecte') && $request->non_affecte == '1') {
            $query->whereNull('agent_id');
        }
        // Un agent ne voit que ses dossiers
        if (Auth::user()->role === 'agent') {
            $query->where('agent_id', Auth::id());
        }

        $dossiers = $query->latest()->paginate(15)->withQueryString();
        $agents   = Utilisateur::where('role', 'agent')->get();

        return view('dossiers.index', compact('dossiers', 'agents'));
    }

    public function create()
    {
        $clients = Client::orderBy('nom')->get();
        $agents  = Utilisateur::where('role', 'agent')->get();

        return view('dossiers.create', compact('clients', 'agents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'   => 'required|exists:clients,id',
            'agent_id'    => 'nullable|exists:utilisateurs,id',
            'titre'       => 'required|string|max:200',
            'description' => 'nullable|string',
        ]);

        $data['statut']   = 'en_attente';
        $data['ouvert_par'] = Auth::id();

        $dossier = Dossier::create($data);

        // ENREGISTREMENT DANS L'HISTORIQUE
        Historique::enregistrer(
            dossierId: $dossier->id,
            action: Historique::ACTION_CREATION,
            details: "Création du dossier '{$dossier->titre}' pour le client ID: {$dossier->client_id}",
            nouvelleValeur: json_encode([
                'titre' => $dossier->titre,
                'client_id' => $dossier->client_id,
                'agent_id' => $dossier->agent_id,
                'statut' => $dossier->statut
            ])
        );

        return redirect()->route('dossiers.show', $dossier)->with('success', 'Dossier créé.');
    }

    public function show(Dossier $dossier)
    {
        // Charger l'historique du dossier
        $dossier->load(['client', 'agent', 'demandes', 'documents', 'ouvertPar']);

        // Récupérer l'historique des actions pour ce dossier
        $historique = Historique::where('dossier_id', $dossier->id)
            ->with('utilisateur')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        $agents = Utilisateur::where('role', 'agent')->orderBy('nom')->get();
        return view('dossiers.show', compact('dossier', 'historique'));
    }

    public function edit(Dossier $dossier)
    {
        $clients = Client::orderBy('nom')->get();
        $agents  = Utilisateur::where('role', 'agent')->get();

        return view('dossiers.edit', compact('dossier', 'clients', 'agents'));
    }

    public function update(Request $request, Dossier $dossier)
    {
        $data = $request->validate([
            'client_id'   => 'required|exists:clients,id',
            'agent_id'    => 'nullable|exists:utilisateurs,id',
            'titre'       => 'required|string|max:200',
            'description' => 'nullable|string',
        ]);

        // Récupérer les anciennes valeurs avant modification
        $anciennesValeurs = [
            'client_id' => $dossier->client_id,
            'agent_id' => $dossier->agent_id,
            'titre' => $dossier->titre,
            'description' => $dossier->description,
        ];

        $dossier->update($data);

        // ENREGISTREMENT DANS L'HISTORIQUE
        $modifications = [];
        if ($anciennesValeurs['titre'] != $dossier->titre) {
            $modifications[] = "Titre: '{$anciennesValeurs['titre']}' → '{$dossier->titre}'";
        }
        if ($anciennesValeurs['client_id'] != $dossier->client_id) {
            $modifications[] = "Client ID: {$anciennesValeurs['client_id']} → {$dossier->client_id}";
        }
        if ($anciennesValeurs['agent_id'] != $dossier->agent_id) {
            $modifications[] = "Agent ID: " . ($anciennesValeurs['agent_id'] ?? 'non assigné') . " → " . ($dossier->agent_id ?? 'non assigné');
        }

        if (!empty($modifications)) {
            Historique::enregistrer(
                dossierId: $dossier->id,
                action: Historique::ACTION_MODIFICATION,
                details: "Modification du dossier: " . implode(', ', $modifications),
                ancienneValeur: json_encode($anciennesValeurs),
                nouvelleValeur: json_encode($data)
            );
        }

        return redirect()->route('dossiers.show', $dossier)->with('success', 'Dossier mis à jour.');
    }

    public function destroy(Dossier $dossier)
    {
        $dossierId = $dossier->id;
        $dossierTitre = $dossier->titre;

        // ENREGISTREMENT DANS L'HISTORIQUE AVANT SUPPRESSION
        Historique::enregistrer(
            dossierId: $dossierId,
            action: Historique::ACTION_SUPPRESSION,
            details: "Suppression du dossier '{$dossierTitre}'",
            ancienneValeur: json_encode($dossier->toArray())
        );

        $dossier->delete();

        return redirect()->route('dossiers.index')->with('success', 'Dossier supprimé.');
    }

    public function updateStatut(Request $request, Dossier $dossier)
    {
        $request->validate([
            'statut' => 'required|in:en_attente,en_cours,valide,rejete',
        ]);

        $ancienStatut = $dossier->statut;
        $statuts = [
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'valide' => 'Validé',
            'rejete' => 'Rejeté',
        ];

        $dossier->update(['statut' => $request->statut]);

        // Enregistrement dans l'historique
        Historique::enregistrer(
            dossierId: $dossier->id,
            action: Historique::ACTION_CHANGEMENT_STATUT,
            details: "Changement de statut de '{$statuts[$ancienStatut]}' à '{$statuts[$request->statut]}'",
            ancienneValeur: $ancienStatut,
            nouvelleValeur: $request->statut
        );
        if ($dossier->agent) {
            $dossier->agent->notify(new DossierStatutNotification($dossier, $ancienStatut, $request->statut));
        }
        // ✅ AJOUTER LE MESSAGE DE SUCCÈS
        return back()->with('success', 'Statut mis à jour avec succès.');
    }
    /**
     * Afficher l'historique complet d'un dossier
     */
    public function historique(Dossier $dossier)
    {
        $historique = Historique::where('dossier_id', $dossier->id)
            ->with('utilisateur')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return view('dossiers.historique', compact('dossier', 'historique'));
    }
    /**
     * Affecter un agent à un dossier
     */
    public function affecterAgent(Request $request, Dossier $dossier)
    {
        // Vérifier que l'utilisateur connecté est admin ou responsable
        $user = Auth::user();

        if (!$user || ($user->role !== 'admin' && $user->role !== 'responsable')) {
            return back()->with('erreur', 'Vous n\'avez pas l\'autorisation d\'affecter un agent.');
        }

        $request->validate([
            'agent_id' => 'required|exists:utilisateurs,id'
        ]);

        $ancienAgentId = $dossier->agent_id;
        $dossier->update(['agent_id' => $request->agent_id]);

        // Enregistrer dans l'historique
        Historique::enregistrer(
            dossierId: $dossier->id,
            action: Historique::ACTION_AFFECTATION_AGENT,
            details: "Agent affecté: " . ($ancienAgentId ? "ancien agent ID {$ancienAgentId} → " : "") . "nouvel agent ID {$request->agent_id}",
            ancienneValeur: $ancienAgentId,
            nouvelleValeur: $request->agent_id
        );

        return back()->with('succes', 'Agent affecté avec succès.');
    }
    public function export(Request $request)
    {
        return Excel::download(
            new DossiersExport($request->input('statut'), $request->input('agent_id')),
            'dossiers_' . date('Y-m-d') . '.xlsx'
        );
    }


    public function exportPdf(Dossier $dossier)
    {
        // Charger les relations nécessaires
        $dossier->load(['client', 'agent', 'demandes', 'documents', 'historique']);

        // Générer le PDF
        $pdf = Pdf::loadView('dossiers.pdf', compact('dossier'));

        // Retourner le PDF en téléchargement
        return $pdf->download('dossier_' . $dossier->numero_dossier . '.pdf');
    }
}
