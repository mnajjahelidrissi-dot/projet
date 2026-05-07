{{-- FICHIER: resources/views/demandes/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Nouvelle demande')
@section('page-title', 'Enregistrer une demande')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <form method="POST" action="{{ route('demandes.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dossier</label>
                <select name="dossier_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Sélectionner un dossier…</option>
                    @foreach($dossiers as $dossier)
                    <option value="{{ $dossier->id }}"
                        {{ old('dossier_id', $dossierSelectionne?->id) == $dossier->id ? 'selected' : '' }}>
                        {{ $dossier->client->nom_complet }} – {{ $dossier->titre }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type de demande</label>
                <select name="type_demande" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @foreach($types as $key => $label)
                    <option value="{{ $key }}" {{ old('type_demande') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="4" required
                          class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                          placeholder="Détails de la demande…">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Montant (optionnel, en MAD)</label>
                <input type="number" name="montant" value="{{ old('montant') }}" step="0.01" min="0"
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                       placeholder="0.00">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-[#0f2744] text-white px-5 py-2 rounded-lg text-sm hover:bg-[#1a4a8a]">Enregistrer</button>
                <a href="{{ route('demandes.index') }}" class="px-5 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
