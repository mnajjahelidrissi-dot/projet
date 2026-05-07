@extends('layouts.app')
@section('titre', 'Nouveau client')

@section('content')

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('clients.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="fw-bold mb-0">Enregistrer un nouveau client</h5>
    </div>

    <div class="card shadow-sm border-0" style="max-width: 750px;">
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

            <form method="POST" action="{{ route('clients.enregistrer') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                            value="{{ old('nom') }}" placeholder="MOUSSAOUI" required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                        <input type="text" name="prenom" class="form-control @error('prenom') is-invalid @enderror"
                            value="{{ old('prenom') }}" placeholder="Ahmed" required>
                        @error('prenom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">CIN <span class="text-danger">*</span></label>
                        <input type="text" name="cin" class="form-control @error('cin') is-invalid @enderror"
                            value="{{ old('cin') }}" placeholder="AB123456" required>
                        @error('cin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Date de naissance</label>
                        <input type="date" name="date_naissance" class="form-control"
                            value="{{ old('date_naissance') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Téléphone <span class="text-danger">*</span></label>
                        <input type="text" name="telephone" class="form-control @error('telephone') is-invalid @enderror"
                            value="{{ old('telephone') }}" placeholder="0661234567" required>
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" placeholder="client@email.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Adresse</label>
                        <input type="text" name="adresse" class="form-control" value="{{ old('adresse') }}"
                            placeholder="12 Rue Hassan II">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Ville</label>
                        <input type="text" name="ville" class="form-control" value="{{ old('ville') }}"
                            placeholder="Casablanca">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Profession</label>
                        <input type="text" name="profession" class="form-control" value="{{ old('profession') }}"
                            placeholder="Ingénieur">
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-saham px-4">
                        <i class="bi bi-check-lg me-1"></i> Enregistrer
                    </button>
                    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary px-4">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>

@endsection
