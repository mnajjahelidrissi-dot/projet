<?php

namespace App\Exports;

use App\Models\Client;
use App\Models\Dossier;
use App\Models\Demande;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StatsExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function array(): array
    {
        $stats = [
            ['Statistique', 'Valeur'],
            ['Total clients', Client::count()],
            ['Clients actifs', Client::where('statut', 'actif')->count()],
            ['Clients inactifs', Client::where('statut', 'inactif')->count()],
            ['', ''],
            ['Total dossiers', Dossier::count()],
            ['Dossiers en attente', Dossier::where('statut', 'en_attente')->count()],
            ['Dossiers en cours', Dossier::where('statut', 'en_cours')->count()],
            ['Dossiers validés', Dossier::where('statut', 'valide')->count()],
            ['Dossiers rejetés', Dossier::where('statut', 'rejete')->count()],
            ['', ''],
            ['Total demandes', Demande::count()],
        ];

        // Ajouter la répartition par type de demande
        $repartition = Demande::select('type_demande', \DB::raw('count(*) as total'))
            ->groupBy('type_demande')
            ->get();

        $labels = [
            'ouverture_compte' => 'Ouverture de compte',
            'demande_carte' => 'Demande de carte',
            'demande_credit' => 'Demande de crédit',
            'reclamation' => 'Réclamation',
            'autre' => 'Autre'
        ];

        $stats[] = ['', ''];
        $stats[] = ['Répartition par type de demande', ''];

        foreach ($repartition as $item) {
            $stats[] = [$labels[$item->type_demande] ?? $item->type_demande, $item->total];
        }

        return $stats;
    }

    public function headings(): array
    {
        return ['Indicateur', 'Valeur'];
    }
}
