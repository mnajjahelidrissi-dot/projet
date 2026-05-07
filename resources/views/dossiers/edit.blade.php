
@extends('layouts.app')
@section('title', 'Modifier dossier')
@section('page-title', 'Modifier: ' . $dossier->titre)

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <form method="POST" action="{{ route('dossiers.mettre-a-jour', $dossier) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                <select name="client_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ $dossier->client_id == $client->id ? 'selected' : '' }}>
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
                    <option value="{{ $agent->id }}" {{ $dossier->agent_id == $agent->id ? 'selected' : '' }}>{{ $agent->nom_complet }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
                <input type="text" name="titre" value="{{ old('titre', $dossier->titre) }}" required
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('description', $dossier->description) }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-[#0f2744] text-sm px-5 py-2 rounded-lg text-sm hover:bg-[#1a4a8a]">Sauvegarder</button>
                <a href="{{ route('dossiers.index') }}" class="px-5 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
