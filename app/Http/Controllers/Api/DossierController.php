<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dossier;
use App\Models\Utilisateur;
use App\Models\Historique;
use App\Notifications\DossierStatutNotification;
use Illuminate\Http\Request;
use App\Exports\DossiersExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class DossierController extends Controller
{
    /**
     * GET /api/dossiers
     * Liste filtrée et paginée des dossiers.
     */
    public function index(Request $request)
    {
        $currentUser = $request->user();
        $query = Dossier::with(['client', 'agent']);

        // Filtres applicables
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        if ($request->filled('non_affecte') && $request->non_affecte == '1') {
            $query->whereNull('agent_id');
        }

        // Restriction : Un agent ne voit que ses dossiers attribués
        if ($currentUser->role === 'agent') {
            $query->where('agent_id', $currentUser->id);
        }

        $dossiers = $query->latest()->paginate(15)->withQueryString();

        return response()->json([
            'success' => true,
            'data' => $dossiers
        ], 200);
    }

    /**
     * POST /api/dossiers
     * Création d'un dossier et écriture dans l'historique.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'   => 'required|exists:clients,id',
            'agent_id'    => 'nullable|exists:utilisateurs,id',
            'titre'       => 'required|string|max:200',
            'description' => 'nullable|string',
        ]);

        $data['statut'] = 'en_attente';
        $data['ouvert_par'] = $request->user()->id;

        $dossier = Dossier::create($data);

        // Historisation de la création
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

        return response()->json([
            'success' => true,
            'message' => 'Dossier créé avec succès.',
            'data' => $dossier->load(['client', 'agent'])
        ], 201);
    }

    /**
     * GET /api/dossiers/{id}
     * Vue détaillée du dossier incluant un premier segment de l'historique.
     */
    public function show(Dossier $dossier)
    {
        $dossier->load(['client', 'agent', 'demandes', 'documents', 'ouvertPar']);

        $historique = Historique::where('dossier_id', $dossier->id)
            ->with('utilisateur')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => [
                'dossier' => $dossier,
                'historique' => $historique
            ]
        ], 200);
    }

    /**
     * PUT/PATCH /api/dossiers/{id}
     * Mise à jour des informations de base du dossier.
     */
    public function update(Request $request, Dossier $dossier)
    {
        $data = $request->validate([
            'client_id'   => 'required|exists:clients,id',
            'agent_id'    => 'nullable|exists:utilisateurs,id',
            'titre'       => 'required|string|max:200',
            'description' => 'nullable|string',
        ]);

        $anciennesValeurs = [
            'client_id' => $dossier->client_id,
            'agent_id' => $dossier->agent_id,
            'titre' => $dossier->titre,
            'description' => $dossier->description,
        ];

        $dossier->update($data);

        // Détection des changements pour l'historique
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
                details: "Modification du dossier: " . implode(',', $modifications),
                ancienneValeur: json_encode($anciennesValeurs),
                nouvelleValeur: json_encode($data)
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Dossier mis à jour avec succès.',
            'data' => $dossier->load(['client', 'agent'])
        ], 200);
    }

    /**
     * DELETE /api/dossiers/{id}
     * Suppression définitive du dossier après historisation.
     */
    public function destroy(Dossier $dossier)
    {
        Historique::enregistrer(
            dossierId: $dossier->id,
            action: Historique::ACTION_SUPPRESSION,
            details: "Suppression du dossier '{$dossier->titre}'",
            ancienneValeur: json_encode($dossier->toArray())
        );

        $dossier->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dossier supprimé avec succès.'
        ], 200);
    }

    /**
     * PATCH /api/dossiers/{id}/statut
     * Mutation du statut du dossier et notification de l'agent.
     */
    public function updateStatut(Request $request, Dossier $dossier)
    {
        $request->validate([
            'statut' => 'required|in:en_attente,en_cours,valide,rejete',
        ]);

        $ancienStatut = $dossier->statut;
        $statuts = [
            'en_attente' => 'En attente',
            'en_cours'   => 'En cours',
            'valide'     => 'Validé',
            'rejete'     => 'Rejeté',
        ];

        $dossier->update(['statut' => $request->statut]);

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

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès.',
            'data' => [
                'id' => $dossier->id,
                'statut' => $dossier->statut
            ]
        ], 200);
    }

    /**
     * GET /api/dossiers/{id}/historique
     * Route dédiée pour consulter la totalité des logs d'un dossier.
     */
    public function historique(Dossier $dossier)
    {
        $historique = Historique::where('dossier_id', $dossier->id)
            ->with('utilisateur')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return response()->json([
            'success' => true,
            'data' => $historique
        ], 200);
    }

    /**
     * PATCH /api/dossiers/{id}/affecter
     * Attribution managériale du dossier à un agent spécifique.
     */
    public function affecterAgent(Request $request, Dossier $dossier)
    {
        $user = $request->user();

        // Correction cohérente avec les rôles du UtilisateurController ('administrateur' au lieu de 'admin')
        if (!$user || ($user->role !== 'administrateur' && $user->role !== 'responsable')) {
            return response()->json([
                'success' => false,
                'message' => "Vous n'avez pas l'autorisation d'affecter un agent."
            ], 403);
        }

        $request->validate([
            'agent_id' => 'required|exists:utilisateurs,id'
        ]);

        $ancienAgentId = $dossier->agent_id;
        $dossier->update(['agent_id' => $request->agent_id]);

        Historique::enregistrer(
            dossierId: $dossier->id,
            action: Historique::ACTION_AFFECTATION_AGENT,
            details: "Agent affecté: " . ($ancienAgentId ? "ancien agent ID {$ancienAgentId} → " : "") . "nouvel agent ID {$request->agent_id}",
            ancienneValeur: $ancienAgentId,
            nouvelleValeur: $request->agent_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Agent affecté avec succès.',
            'data' => $dossier->load('agent')
        ], 200);
    }

    /**
     * GET /api/dossiers/export/excel
     * Exportation groupée de l'index des dossiers en format Excel.
     */
    public function export(Request $request)
    {
        return Excel::download(
            new DossiersExport($request->input('statut'), $request->input('agent_id')),
            'dossiers_' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * GET /api/dossiers/{id}/export/pdf
     * Génération et envoi du flux binaire PDF pour impression.
     */
    public function exportPdf(Dossier $dossier)
    {
        $dossier->load(['client', 'agent', 'demandes', 'documents', 'historique']);

        $pdf = Pdf::loadView('dossiers.pdf', compact('dossier'));

        return $pdf->download('dossier_' . $dossier->numero_dossier . '.pdf');
    }
}
