<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Dossier;
use App\Models\Utilisateur;
use App\Models\Demande;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\StatsExport;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index()
    {
        $utilisateur = Auth::user();

        // Vérifier si l'utilisateur est connecté
        if (!$utilisateur) {
            return redirect()->route('login');
        }

        $utilisateur = Utilisateur::find($utilisateur->id);

        //  Statisitiques de base
        $stats = [
            'total_clients'   => Client::count(),
            'total_dossiers'  => Dossier::count(),
            'total_demandes'  => Demande::count(),
            'en_attente'      => Dossier::where('statut', 'en_attente')->count(),
            'en_cours'        => Dossier::where('statut', 'en_cours')->count(),
            'valide'          => Dossier::where('statut', 'valide')->count(),
            'rejete'          => Dossier::where('statut', 'rejete')->count(),
        ];

        // . STATISTIQUES SPÉCIFIQUES SELON LE RÔLE
        if ($utilisateur->estAdministrateur()) {
            // Admin voit toutes les statistiques
            $stats['role_label'] = 'Administrateur';
        } elseif ($utilisateur->estResponsable()) {
            // Responsable voit toutes les statistiques aussi
            $stats['role_label'] = 'Responsable';
        } elseif ($utilisateur->estAgent()) {
            // Agent voit seulement ses propres statistiques
            $stats['mes_dossiers']    = Dossier::where('agent_id', $utilisateur->id())->count();
            $stats['mes_en_cours']    = Dossier::where('agent_id', $utilisateur->id())->where('statut', 'en_cours')->count();
            $stats['mes_en_attente']  = Dossier::where('agent_id', $utilisateur->id())->where('statut', 'en_attente')->count();
            $stats['role_label'] = 'Agent';
        }

        //  RÉPARTITION PAR TYPE DE DEMANDE
        $repartition = Demande::select('type_demande', DB::raw('count(*) as total'))
            ->groupBy('type_demande')
            ->pluck('total', 'type_demande');

        //  RÉPARTITION PAR STATUT (pour graphique)
        $repartitionTypes = Dossier::select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->get()
            ->map(function ($item) {
                $statuts = [
                    'en_attente' => 'En attente',
                    'en_cours'   => 'En cours',
                    'valide'     => 'Validé',
                    'rejete'     => 'Rejeté',
                ];
                return [
                    'type'  => $statuts[$item->statut] ?? $item->statut,
                    'total' => $item->total,
                ];
            });

        // DERNIERS DOSSIERS (selon le rôle)
        $derniersDossiers = Dossier::with(['client', 'agent'])
            ->when($utilisateur->estAgent(), function ($query) use ($utilisateur) {
                return $query->where('agent_id', $utilisateur->id);
            })
            ->latest()
            ->take(10)
            ->get();

        //  DOSSIERS RÉCENTS PAR STATUT (pour admin/responsable)
        $dossiersParStatut = [];
        if ($utilisateur->estAdministrateur() || $utilisateur->estResponsable()) {
            $dossiersParStatut = [
                'en_attente' => Dossier::with(['client', 'agent'])->where('statut', 'en_attente')->latest()->take(5)->get(),
                'en_cours' => Dossier::with(['client', 'agent'])->where('statut', 'en_cours')->latest()->take(5)->get(),
                'valide' => Dossier::with(['client', 'agent'])->where('statut', 'valide')->latest()->take(5)->get(),
                'rejete' => Dossier::with(['client', 'agent'])->where('statut', 'rejete')->latest()->take(5)->get(),
            ];
        }
        $topAgents = [];
        if ($utilisateur->estAdministrateur() || $utilisateur->estResponsable()) {
            $topAgents = Utilisateur::where('role', 'agent')
                ->withCount('dossiersAffectes')
                ->having('dossiers_affectes_count', '>', 0)
                ->orderBy('dossiers_affectes_count', 'desc')
                ->take(5)
                ->get();
        }

        return view('dashboard.index', compact(
            'stats',
            'repartition',
            'repartitionTypes',
            'derniersDossiers',
            'dossiersParStatut',
            'topAgents',
            'utilisateur'
        ));
    }

    public function exportStats()
    {
        // Vérifier que seul admin ou responsable peut exporter
        $utilisateur = Utilisateur::find(Auth::id());

        if (!$utilisateur || (!$utilisateur->estAdministrateur() && !$utilisateur->estResponsable())) {
            return back()->with('error', 'Vous n\'avez pas l\'autorisation d\'exporter les statistiques.');
        }

        return Excel::download(new StatsExport, 'statistiques_' . date('Y-m-d') . '.xlsx');
    }
}
