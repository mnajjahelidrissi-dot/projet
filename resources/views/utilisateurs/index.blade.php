@extends('layouts.app')
@section('titre', 'Gestion des utilisateurs')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">
            <i class="bi bi-person-gear me-2" style="color:var(--saham-vert)"></i>Utilisateurs
        </h5>
        <a href="{{ route('utilisateurs.creer') }}" class="btn btn-saham">
            <i class="bi bi-person-plus me-1"></i> Nouvel utilisateur
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom complet</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Téléphone</th>
                            <th>Statut</th>
                            <th>Créé le</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($utilisateurs as $utilisateur)
                            <tr>
                                <td class="fw-semibold">
                                    {{ $utilisateur->nom_complet }}
                                    @if ($utilisateur->id === auth()->id())
                                        <span class="badge bg-secondary ms-1">Vous</span>
                                    @endif
                                </td>
                                <td>{{ $utilisateur->email }}</td>
                                <td>
                                    <span
                                        class="badge
                                {{ $utilisateur->role === 'administrateur'
                                    ? 'bg-danger'
                                    : ($utilisateur->role === 'responsable'
                                        ? 'bg-warning text-dark'
                                        : 'bg-secondary') }}">
                                        {{ ucfirst($utilisateur->role) }}
                                    </span>
                                </td>
                                <td>{{ $utilisateur->telephone ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $utilisateur->actif ? 'bg-success' : 'bg-danger' }}">
                                        {{ $utilisateur->actif ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td><small>{{ $utilisateur->created_at->format('d/m/Y') }}</small></td>
                                <td class="text-end">
                                    <a href="{{ route('utilisateurs.modifier', $utilisateur) }}"
                                        class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if ($utilisateur->id !== auth()->id())
                                        <form method="POST"
                                            action="{{ route('utilisateurs.basculer-status', $utilisateur) }}"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm {{ $utilisateur->actif ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                title="{{ $utilisateur->actif ? 'Désactiver' : 'Activer' }}">
                                                <i
                                                    class="bi {{ $utilisateur->actif ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">Aucun utilisateur</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">
        {{ $utilisateurs->links() }}
    </div>

@endsection
