@extends('layouts.app')
@section('titre', 'Clients')

@section('contenu')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0"><i class="bi bi-people me-2" style="color:var(--saham-vert)"></i>Liste des clients</h5>
        <a href="{{ route('clients.creer') }}" class="btn btn-saham">
            <i class="bi bi-person-plus me-1"></i> Nouveau client
        </a>
    </div>

    <!-- Barre de recherche -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('clients.index') }}" class="d-flex gap-2">
                <input type="text" name="recherche" class="form-control"
                    placeholder="Rechercher par nom, prénom, CIN, téléphone..." value="{{ $recherche }}">
                <button type="submit" class="btn btn-saham px-3">
                    <i class="bi bi-search"></i>
                </button>
                @if ($recherche)
                    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary px-3">
                        <i class="bi bi-x"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Tableau -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N° Client</th>
                            <th>Nom & Prénom</th>
                            <th>CIN</th>
                            <th>Téléphone</th>
                            <th>Ville</th>
                            <th>Statut</th>
                            <th>Dossiers</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            <tr>
                                <td><code>{{ $client->numero_client }}</code></td>
                                <td class="fw-semibold">{{ $client->nom_complet }}</td>
                                <td>{{ $client->cin }}</td>
                                <td>{{ $client->telephone }}</td>
                                <td>{{ $client->ville ?? '—' }}</td>
                                <td>
                                    <span
                                        class="badge {{ $client->statut === 'actif' ? 'bg-success' : 'bg-secondary' }} badge-statut">
                                        {{ ucfirst($client->statut) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary rounded-pill">
                                        {{ $client->dossiers_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('clients.afficher', $client) }}"
                                        class="btn btn-sm btn-outline-secondary me-1" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('clients.modifier', $client) }}"
                                        class="btn btn-sm btn-outline-primary me-1" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if (auth()->user()->estAdministrateur() || auth()->user()->estResponsable())
                                        <form method="POST" action="{{ route('clients.supprimer', $client) }}"
                                            class="d-inline" onsubmit="return confirm('Supprimer ce client ?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Aucun client trouvé
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-3 d-flex justify-content-center">
        {{ $clients->withQueryString()->links() }}
    </div>

@endsection
