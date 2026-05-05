@extends('layouts.app')
@section('titre', 'Modifier l\'utilisateur')

@section('contenu')

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('utilisateurs.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="fw-bold mb-0">Modifier — {{ $utilisateur->nom_complet }}</h5>
    </div>

    <div class="card shadow-sm border-0" style="max-width: 600px;">
        <div class="card-body p-4">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $erreur)
                            <li>{{ $erreur }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('utilisateurs.mettre-a-jour', $utilisateur) }}">
                @csrf @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control"
                            value="{{ old('nom', $utilisateur->nom) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                        <input type="text" name="prenom" class="form-control"
                            value="{{ old('prenom', $utilisateur->prenom) }}" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control"
                            value="{{ old('email', $utilisateur->email) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Rôle <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="administrateur"
                                {{ old('role', $utilisateur->role) === 'administrateur' ? 'selected' : '' }}>Administrateur
                            </option>
                            <option value="responsable"
                                {{ old('role', $utilisateur->role) === 'responsable' ? 'selected' : '' }}>Responsable
                            </option>
                            <option value="agent"
                                {{ old('role', $utilisateur->role) === 'agent' ? 'selected' : '' }}>Agent</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Téléphone</label>
                        <input type="text" name="telephone" class="form-control"
                            value="{{ old('telephone', $utilisateur->telephone) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Statut</label>
                        <div class="form-check mt-2">
                            <input type="checkbox" name="actif" id="actif" class="form-check-input" value="1"
                                {{ old('actif', $utilisateur->actif) ? 'checked' : '' }}>
                            <label class="form-check-label" for="actif">Compte actif</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <hr>
                        <p class="text-muted small">Laisser vide pour ne pas changer le mot de passe.</p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nouveau mot de passe</label>
                        <input type="password" name="password" class="form-control" minlength="8">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation" class="form-control" minlength="8">
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-saham px-4">
                        <i class="bi bi-check-lg me-1"></i> Mettre à jour
                    </button>
                    <a href="{{ route('utilisateurs.index') }}" class="btn btn-outline-secondary px-4">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>

@endsection
