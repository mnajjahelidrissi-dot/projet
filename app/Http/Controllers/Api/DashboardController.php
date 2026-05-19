<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Dossier;
use App\Models\Utilisateur;
use App\Models\Demande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\StatsExport;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    /**
     * GET /api/dashboard/admin
     * Dashboard pour ADMIN et RESPONSABLE (statistiques globales)
     */
    public function adminDashboard(Request $request)
    {
        $utilisateur = $request->user();

        if (!$utilisateur) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        // Vérifier que l'utilisateur est admin ou responsable
        if (!$utilisateur->estAdministrateur() && !$utilisateur->estResponsable()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé'
            ], 403);
        }

        // ========== STATISTIQUES GLOBALES ==========
        $stats = [
            'role_label'     => $utilisateur->estAdministrateur() ? 'Administrateur' : 'Responsable',
            'total_clients'  => Client::count(),
            'total_dossiers' => Dossier::count(),
            'total_demandes' => Demande::count(),
            'en_attente'     => Dossier::where('statut', 'en_attente')->count(),
            'en_cours'       => Dossier::where('statut', 'en_cours')->count(),
            'valide'         => Dossier::where('statut', 'valide')->count(),
            'rejete'         => Dossier::where('statut', 'rejete')->count(),
        ];

        // ========== RÉPARTITION PAR TYPE DE DEMANDE ==========
        $repartitionDemandes = [];

        if (Demande::count() > 0) {
            $repartitionDemandes = Demande::select('type_demande', DB::raw('count(*) as total'))
                ->groupBy('type_demande')
                ->pluck('total', 'type_demande')
                ->toArray();
        }

        // ========== RÉPARTITION PAR STATUT DE DOSSIER ==========
        $repartitionDossiers = [];

        if (Dossier::count() > 0) {
            $statutsLabels = [
                'en_attente' => 'En attente',
                'en_cours'   => 'En cours',
                'valide'     => 'Validé',
                'rejete'     => 'Rejeté',
            ];

            $repartitionDossiers = Dossier::select('statut', DB::raw('count(*) as total'))
                ->groupBy('statut')
                ->get()
                ->map(function ($item) use ($statutsLabels) {
                    return [
                        'status_key' => $item->statut,
                        'label'      => $statutsLabels[$item->statut] ?? $item->statut,
                        'total'      => $item->total,
                    ];
                });
        }

        // ========== DERNIERS DOSSIERS ==========
        $derniersDossiers = Dossier::with(['client', 'agent'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($dossier) {
                return [
                    'id'            => $dossier->id,
                    'titre'         => $dossier->titre,
                    'statut'        => $dossier->statut,
                    'statut_label'  => $dossier->statut_label,
                    'created_at'    => $dossier->created_at,
                    'client'        => $dossier->client ? [
                        'id'          => $dossier->client->id,
                        'nom_complet' => $dossier->client->nom_complet,
                    ] : null,
                    'agent'         => $dossier->agent ? [
                        'id'          => $dossier->agent->id,
                        'nom_complet' => $dossier->agent->nom_complet,
                    ] : null,
                ];
            });

        // ========== TOP AGENTS ==========
        $topAgents = [];

        try {
            $topAgents = Utilisateur::where('role', 'agent')
                ->withCount('dossiersAffectes')
                ->having('dossiers_affectes_count', '>', 0)
                ->orderBy('dossiers_affectes_count', 'desc')
                ->take(5)
                ->get()
                ->map(function ($agent) {
                    return [
                        'id'          => $agent->id,
                        'nom_complet' => $agent->nom_complet,
                        'dossiers_count' => $agent->dossiers_affectes_count,
                    ];
                });
        } catch (\Exception $e) {
            $topAgents = [];
        }

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'repartitionDemandes' => $repartitionDemandes,
            'repartitionDossiers' => $repartitionDossiers,
            'derniersDossiers' => $derniersDossiers,
            'topAgents' => $topAgents
        ]);
    }

    /**
     * GET /api/dashboard/agent
     * Dashboard pour AGENT (statistiques personnelles)
     */
    public function agentDashboard(Request $request)
    {
        $utilisateur = $request->user();

        if (!$utilisateur) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        // Vérifier que l'utilisateur est agent
        if (!$utilisateur->estAgent()) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé'
            ], 403);
        }

        // ========== STATISTIQUES PERSONNELLES ==========
        $stats = [
            'role_label'      => 'Agent',
            'mes_dossiers'    => Dossier::where('agent_id', $utilisateur->id)->count(),
            'mes_en_cours'    => Dossier::where('agent_id', $utilisateur->id)->where('statut', 'en_cours')->count(),
            'mes_en_attente'  => Dossier::where('agent_id', $utilisateur->id)->where('statut', 'en_attente')->count(),
            'mes_valides'     => Dossier::where('agent_id', $utilisateur->id)->where('statut', 'valide')->count(),
        ];

        // ========== MES DERNIERS DOSSIERS ==========
        $mesDossiers = Dossier::with(['client'])
            ->where('agent_id', $utilisateur->id)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($dossier) {
                return [
                    'id'            => $dossier->id,
                    'titre'         => $dossier->titre,
                    'statut'        => $dossier->statut,
                    'statut_label'  => $dossier->statut_label,
                    'created_at'    => $dossier->created_at,
                    'client'        => $dossier->client ? [
                        'id'          => $dossier->client->id,
                        'nom_complet' => $dossier->client->nom_complet,
                    ] : null,
                ];
            });

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'derniersDossiers' => $mesDossiers
        ]);
    }

    /**
     * GET /api/dashboard
     * Dashboard unique selon le rôle (version simplifiée)
     */
    public function index(Request $request)
    {
        $utilisateur = $request->user();

        if (!$utilisateur) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        if ($utilisateur->estAdministrateur() || $utilisateur->estResponsable()) {
            return $this->adminDashboard($request);
        } else {
            return $this->agentDashboard($request);
        }
    }

    /**
     * GET /api/dashboard/export
     * Export Excel sécurisé des métriques du dashboard.
     */
    public function exportStats(Request $request)
    {
        $utilisateur = $request->user();

        if (!$utilisateur || (!$utilisateur->estAdministrateur() && !$utilisateur->estResponsable())) {
            return response()->json([
                'success' => false,
                'message' => "Vous n'avez pas l'autorisation d'exporter les statistiques de l'entreprise."
            ], 403);
        }

        return Excel::download(new StatsExport, 'statistiques_' . date('Y-m-d') . '.xlsx');
    }
}
