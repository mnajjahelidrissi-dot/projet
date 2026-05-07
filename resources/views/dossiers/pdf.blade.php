<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dossier {{ $dossier->numero_dossier }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #0f2744;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            background-color: #f0f0f0;
            padding: 8px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td, table th {
            border: 1px solid #ddd;
            padding: 8px;
            vertical-align: top;
        }
        table th {
            background-color: #f5f5f5;
            text-align: left;
            width: 30%;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-en_attente { background-color: #fef3c7; color: #92400e; }
        .badge-en_cours { background-color: #dbeafe; color: #1e40af; }
        .badge-valide { background-color: #d1fae5; color: #065f46; }
        .badge-rejete { background-color: #fee2e2; color: #991b1b; }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SAHAM BANK</h1>
        <p>Fiche de dossier client</p>
    </div>

    <div class="section">
        <div class="section-title">Informations générales</div>
        <table>
            <tr><th>N° Dossier</th><td>{{ $dossier->numero_dossier ?? $dossier->id }}</td></tr>
            <tr><th>Titre</th><td>{{ $dossier->titre }}</td></tr>
            <tr><th>Statut</th>
                <td><span class="badge badge-{{ $dossier->statut }}">{{ $dossier->statut_label }}</span></td>
            </tr>
            <tr><th>Date de création</th><td>{{ $dossier->created_at->format('d/m/Y H:i') }}</td></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Client</div>
        <table>
            <tr><th>Nom complet</th><td>{{ $dossier->client->nom_complet }}</td></tr>
            <tr><th>CIN</th><td>{{ $dossier->client->cin }}</td></tr>
            <tr><th>Téléphone</th><td>{{ $dossier->client->telephone }}</td></tr>
            @if($dossier->client->email)<tr><th>Email</th><td>{{ $dossier->client->email }}</td></tr>@endif
            @if($dossier->client->adresse)<tr><th>Adresse</th><td>{{ $dossier->client->adresse }}</td></tr>@endif
        </table>
    </div>

    <div class="section">
        <div class="section-title">Agent assigné</div>
        <table>
            <tr><th>Agent</th>
                <td>{{ $dossier->agent?->nom_complet ?? 'Non assigné' }}</td>
            </tr>
        </table>
    </div>

    @if($dossier->description)
    <div class="section">
        <div class="section-title">Description</div>
        <table>
            <tr><td>{{ $dossier->description }}</td></tr>
        </table>
    </div>
    @endif

    @if($dossier->demandes->count() > 0)
    <div class="section">
        <div class="section-title">Demandes ({{ $dossier->demandes->count() }})</div>
        <table>
            <thead>
                <tr><th>Type</th><th>Description</th><th>Montant</th><th>Date</th></tr>
            </thead>
            <tbody>
                @foreach($dossier->demandes as $demande)
                <tr>
                    <td>{{ \App\Http\Controllers\DemandeController::TYPES[$demande->type_demande] ?? $demande->type_demande }}</td>
                    <td>{{ $demande->description }}</td>
                    <td>{{ $demande->montant ? number_format($demande->montant, 2) . ' MAD' : '—' }}</td>
                    <td>{{ $demande->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($dossier->documents->count() > 0)
    <div class="section">
        <div class="section-title">Documents archivés ({{ $dossier->documents->count() }})</div>
        <table>
            <thead>
                <tr><th>Type</th><th>Nom du fichier</th><th>Taille</th><th>Date</th></tr>
            </thead>
            <tbody>
                @foreach($dossier->documents as $doc)
                <tr>
                    <td>{{ \App\Http\Controllers\DocumentController::TYPES[$doc->type_document] ?? $doc->type_document }}</td>
                    <td>{{ $doc->nom_fichier }}</td>
                    <td>{{ $doc->taille_formatee }}</td>
                    <td>{{ $doc->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($dossier->historique->count() > 0)
    <div class="section">
        <div class="section-title">Historique des actions ({{ $dossier->historique->count() }})</div>
        <table>
            <thead>
                <tr><th>Date</th><th>Action</th><th>Détails</th><th>Utilisateur</th></tr>
            </thead>
            <tbody>
                @foreach($dossier->historique->take(20) as $hist)
                <tr>
                    <td>{{ $hist->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $hist->action_label }}</td>
                    <td>{{ $hist->details }}</td>
                    <td>{{ $hist->utilisateur?->nom_complet ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }} - SAHAM BANK
    </div>
</body>
</html>
