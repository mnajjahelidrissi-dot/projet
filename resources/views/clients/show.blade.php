@extends('layouts.app')
@section('titre', 'Client : ' . $client->nom_complet)

@section('contenu')

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('clients.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="fw-bold mb-0">Fiche client</h5>
        <span class="ms-auto">
            <a href="{{ route('clients.modifier', $client) }}" class="btn btn-sm btn-outline-primary me-1">
                <i class="bi bi-pencil me-1"></i> Modifier
            </a>
            <a href="{{ route('dossiers.creer', ['client_id' => $client->id]) }}" class="btn btn-sm btn-saham">
                <i class="bi bi-folder-plus me-1"></i> Nouveau dossier
            </a>
        </span>
    </div>

    <div class="row g-3">
        <!-- Informations personnelles -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom fw-semibold py-3">
                    <i class="bi bi-person me-2" style="color:var(--saham-vert)"></i> Informations personnelles
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted fw-semibold" style="width:45%">N° Client</td>
                            <td><code>{{ $client->numero_client }}</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Nom complet</td>
                            <td class="fw-bold">{{ $client->nom_complet }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">CIN</td>
                            <td>{{ $client->cin }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Date de naissance</td>
                            <td>{{ $client->date_naissance ? $client->date_naissance->format('d/m/Y') : '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Téléphone</td>
                            <td>{{ $client->telephone }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Email</td>
                            <td>{{ $client->email ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Adresse</td>
                            <td>{{ $client->adresse ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Ville</td>
                            <td>{{ $client->ville ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Profession</td>
                            <td>{{ $client->profession ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Statut</td>
                            <td>
                                <span class="badge {{ $client->statut === 'actif' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($client->statut) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Enregistré le</td>
                            <td>{{ $client->created_at->format('d/m/Y à H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Enregistré par</td>
                            <td>{{ $client->createur?->nom_complet ?? '—' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Dossiers du client -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom fw-semibold py-3">
                    <i class="bi bi-folder2-open me-2" style="color:var(--saham-or)"></i>
                    Dossiers ({{ $client->dossiers->count() }})
                </div>
                <div class="card-body p-0">
                    @forelse($client->dossiers as $dossier)
                        <div class="border-bottom px-3 py-2 d-flex align-items-center">
                            <div class="me-3">
                                <span class="badge bg-{{ $dossier->couleur_statut }} badge-statut">
                                    {{ $dossier->label_statut }}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">
                                    <code>{{ $dossier->numero_dossier }}</code>
                                    — {{ $dossier->label_type_demande }}
                                </div>
                                <small class="text-muted">
                                    Créé le {{ $dossier->created_at->format('d/m/Y') }}
                                    @if ($dossier->agent)
                                        — Agent : {{ $dossier->agent->nom_complet }}
                                    @endif
                                </small>
                            </div>
                            <a href="{{ route('dossiers.afficher', $dossier) }}"
                                class="btn btn-sm btn-outline-secondary ms-2">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-folder2 fs-3 d-block mb-2"></i>
                            Aucun dossier pour ce client
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection
