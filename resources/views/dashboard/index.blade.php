{{-- FICHIER: resources/views/dashboard/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')
    {{-- ── KPIs ──────────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach ([['Total dossiers', $stats['total_dossiers'], 'bg-blue-50', 'text-blue-700', 'border-blue-100'], ['En attente', $stats['en_attente'], 'bg-yellow-50', 'text-yellow-700', 'border-yellow-100'], ['En cours', $stats['en_cours'], 'bg-indigo-50', 'text-indigo-700', 'border-indigo-100'], ['Validés', $stats['valide'], 'bg-green-50', 'text-green-700', 'border-green-100']] as [$label, $value, $bg, $text, $border])
            <div class="rounded-xl border {{ $border }} {{ $bg }} p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $label }}</p>
                <p class="text-3xl font-bold {{ $text }} mt-1">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- ── Répartition par type ──────────────────────────────────── --}}
        <div class="lg:col-span-1 bg-white rounded-xl border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-700 mb-4">Demandes par type</h3>
            @php
                $typeLabels = [
                    'ouverture_compte' => 'Ouverture compte',
                    'demande_carte' => 'Carte bancaire',
                    'demande_credit' => 'Crédit',
                    'reclamation' => 'Réclamation',
                    'autre' => 'Autre',
                ];
                $total = $repartition->sum() ?: 1;
            @endphp
            <div class="space-y-3">
                @foreach ($repartition as $type => $count)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ $typeLabels[$type] ?? $type }}</span>
                            <span class="font-medium">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ round(($count / $total) * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
                @if ($repartition->isEmpty())
                    <p class="text-gray-400 text-sm text-center py-4">Aucune demande</p>
                @endif
            </div>
        </div>

        {{-- ── Derniers dossiers ─────────────────────────────────────── --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-700">Derniers dossiers</h3>
                <a href="{{ route('dossiers.index') }}" class="text-blue-600 text-xs hover:underline">Voir tout →</a>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 border-b">
                        <th class="pb-2 font-medium">Client</th>
                        <th class="pb-2 font-medium">Titre</th>
                        <th class="pb-2 font-medium">Agent</th>
                        <th class="pb-2 font-medium">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($derniersDossiers as $d)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2.5">
                                <a href="{{ route('clients.show', $d->client) }}"
                                    class="font-medium text-gray-800 hover:text-blue-600">
                                    {{ $d->client->nom_complet }}
                                </a>
                            </td>
                            <td class="py-2.5">
                                <a href="{{ route('dossiers.show', $d) }}"
                                    class="text-gray-600 hover:text-blue-600 truncate max-w-[160px] block">
                                    {{ $d->titre }}
                                </a>
                            </td>
                            <td class="py-2.5 text-gray-500">{{ $d->agent?->name ?? '–' }}</td>
                            <td class="py-2.5">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium badge-{{ $d->statut }}">
                                    {{ $d->statut_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-gray-400">Aucun dossier</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    <div class="grid grid-cols-2 gap-4 mt-6">
        <div class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-10 h-10 bg-teal-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_clients'] }}</p>
                <p class="text-sm text-gray-500">Clients enregistrés</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_demandes'] }}</p>
                <p class="text-sm text-gray-500">Demandes totales</p>
            </div>
        </div>
    </div>
@endsection
