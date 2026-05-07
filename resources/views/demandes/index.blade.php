{{-- FICHIER: resources/views/demandes/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Demandes')
@section('page-title', 'Toutes les demandes')

@section('content')
<form method="GET" class="mb-4 flex gap-3">
    <select name="type" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none">
        <option value="">Tous les types</option>
        @foreach($types as $key => $label)
            <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <button class="px-4 py-2 bg-gray-100 rounded-lg text-sm hover:bg-gray-200">Filtrer</button>
</form>

<div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr class="text-left text-xs text-gray-500 uppercase tracking-wide">
                <th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Client</th>
                <th class="px-4 py-3">Dossier</th>
                <th class="px-4 py-3">Montant</th>
                <th class="px-4 py-3">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($demandes as $demande)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <span class="text-xs font-medium bg-purple-50 text-purple-700 px-2 py-0.5 rounded">
                        {{ $types[$demande->type_demande] ?? $demande->type_demande }}
                    </span>
                </td>
                <td class="px-4 py-3 font-medium">{{ $demande->dossier->client->nom_complet }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('dossiers.show', $demande->dossier) }}" class="text-blue-600 hover:underline text-xs">
                        {{ $demande->dossier->titre }}
                    </a>
                </td>
                <td class="px-4 py-3 text-gray-600">
                    {{ $demande->montant ? number_format($demande->montant, 2) . ' MAD' : '–' }}
                </td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $demande->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Aucune demande</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $demandes->links() }}</div>
@endsection
