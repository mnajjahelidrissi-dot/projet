<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Dossier;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    public function index()
    {
       $utilisateur = Auth::user();
       // On recharge l'utilisateur depuis la base pour s'assurer d'avoir les dernières données (notamment le rôle)
       //pour builder la confiance que cette objet est de type classe Utilisateur et
       $utilisateur=Utilisateur::find($utilisateur->id);


        $stats = [
            'total_clients'   => Client::count(),
            'total_dossiers'  => Dossier::count(),
            'en_attente'      => Dossier::where('statut', 'en_attente')->count(),
            'en_cours'        => Dossier::where('statut', 'en_cours')->count(),
            'valides'         => Dossier::where('statut', 'valide')->count(),
            'rejetes'         => Dossier::where('statut', 'rejete')->count(),
        ];

        // Si l'utilisateur est agent, afficher seulement ses dossiers
        if ($utilisateur->estAgent()) {
            $stats['mes_dossiers']    = Dossier::where('agent_id', $utilisateur->id)->count();
            $stats['mes_en_cours']    = Dossier::where('agent_id', $utilisateur->id)->where('statut', 'en_cours')->count();
            $stats['mes_en_attente']  = Dossier::where('agent_id', $utilisateur->id)->where('statut', 'en_attente')->count();
        }

        // Répartition par type de demande (pour le graphique)
        $repartitionTypes = Dossier::select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->get()
            ->map(function ($item) {
                return [
                    'type'  => Dossier::$typesDemandes[$item->type_demande] ?? $item->type_demande,
                    'total' => $item->total,
                ];
            });

        // Dossiers récents (les 5 derniers)
        $dossiersRecents = Dossier::with(['client', 'agent'])
            ->when($utilisateur->estAgent(), fn($q) => $q->where('agent_id', $utilisateur->id))
            ->latest()
            ->take(5)
            ->get();
       return view('dashboard.index', compact('stats', 'repartitionTypes', 'dossiersRecents'));
    }
}
