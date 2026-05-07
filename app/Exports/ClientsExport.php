<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filtres;

    public function __construct($filtres = null)
    {
        $this->filtres = $filtres;
    }

    public function collection()
    {
        $query = Client::with('createur');

        if ($this->filtres && isset($this->filtres['recherche'])) {
            $recherche = $this->filtres['recherche'];
            $query->where(function($q) use ($recherche) {
                $q->where('nom', 'like', "%{$recherche}%")
                  ->orWhere('prenom', 'like', "%{$recherche}%")
                  ->orWhere('cin', 'like', "%{$recherche}%")
                  ->orWhere('numero_client', 'like', "%{$recherche}%")
                  ->orWhere('telephone', 'like', "%{$recherche}%");
            });
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'N° Client',
            'Nom complet',
            'CIN',
            'Téléphone',
            'Email',
            'Ville',
            'Profession',
            'Statut',
            'Date de création',
            'Créé par'
        ];
    }

    public function map($client): array
    {
        return [
            $client->numero_client,
            $client->nom_complet,
            $client->cin,
            $client->telephone,
            $client->email ?? '—',
            $client->ville ?? '—',
            $client->profession ?? '—',
            $client->statut === 'actif' ? 'Actif' : 'Inactif',
            $client->created_at->format('d/m/Y H:i'),
            $client->createur?->nom_complet ?? '—'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
