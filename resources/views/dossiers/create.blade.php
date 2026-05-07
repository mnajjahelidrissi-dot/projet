{{-- FICHIER: resources/views/dossiers/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Nouveau dossier')
@section('page-title', 'Créer un dossier')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <form method="POST" action="{{ route('dossiers.enregistrer') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                <select name="client_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Sélectionner un client…</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id || old('client_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->nom_complet }} ({{ $client->cin }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Agent assigné</label>
                <select name="agent_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Non assigné</option>
                    @foreach($agents as $agent)
                    <option value="{{ $agent->id }}" {{ old('agent_id') == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Titre du dossier</label>
                <input type="text" name="titre" value="{{ old('titre') }}" required
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                       placeholder="Ex: Demande de crédit immobilier">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description (optionnel)</label>
                <textarea name="description" rows="3"
                          class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('description') }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-[#0f2744] text-white px-5 py-2 rounded-lg text-sm hover:bg-[#1a4a8a]">Créer le dossier</button>
                <a href="{{ route('dossiers.index') }}" class="px-5 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
