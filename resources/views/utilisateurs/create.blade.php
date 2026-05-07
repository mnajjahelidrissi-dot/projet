@extends('layouts.app')
@section('titre', 'Nouvel utilisateur')

@section('content')

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('utilisateurs.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="fw-bold mb-0">Créer un utilisateur</h5>
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

            <form method="POST" action="{{ route('utilisateurs.enregistrer') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                            value="{{ old('nom') }}" required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                        <input type="text" name="prenom" class="form-control @error('prenom') is-invalid @enderror"
                            value="{{ old('prenom') }}" required>
                        @error('prenom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Rôle <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">— Choisir —</option>
                            <option value="administrateur" {{ old('role') === 'administrateur' ? 'selected' : '' }}>
                                Administrateur</option>
                            <option value="responsable" {{ old('role') === 'responsable' ? 'selected' : '' }}>
                                Responsable</option>
                            <option value="agent" {{ old('role') === 'agent' ? 'selected' : '' }}>Agent</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Téléphone</label>
                        <input type="text" name="telephone" class="form-control" value="{{ old('telephone') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Mot de passe <span class="text-danger">*</span></label>
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" required minlength="8">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Confirmer le mot de passe <span
                                class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required
                            minlength="8">
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-saham px-4">
                        <i class="bi bi-check-lg me-1"></i> Créer
                    </button>
                    <a href="{{ route('utilisateurs.index') }}" class="btn btn-outline-secondary px-4">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>

@endsection
