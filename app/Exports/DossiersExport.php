<?php

namespace App\Exports;

use App\Models\Dossier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DossiersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $statut;
    protected $agentId;

    public function __construct($statut = null, $agentId = null)
    {
        $this->statut = $statut;
        $this->agentId = $agentId;
    }

    public function collection()
    {
        $query = Dossier::with(['client', 'agent']);

        if ($this->statut) {
            $query->where('statut', $this->statut);
        }

        if ($this->agentId) {
            $query->where('agent_id', $this->agentId);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Titre',
            'Client',
            'CIN Client',
            'Agent assigné',
            'Statut',
            'Description',
            'Date de création',
            'Dernière modification'
        ];
    }

    public function map($dossier): array
    {
        $statuts = [
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'valide' => 'Validé',
            'rejete' => 'Rejeté'
        ];

        return [
            $dossier->id,
            $dossier->titre,
            $dossier->client?->nom_complet ?? '—',
            $dossier->client?->cin ?? '—',
            $dossier->agent?->nom_complet ?? 'Non affecté',
            $statuts[$dossier->statut] ?? $dossier->statut,
            $dossier->description ?? '—',
            $dossier->created_at->format('d/m/Y H:i'),
            $dossier->updated_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
