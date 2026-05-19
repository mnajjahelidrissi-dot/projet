{{-- FICHIER: resources/views/dossiers/show.blade.php --}}
@extends('layouts.app')
@section('title', $dossier->titre)
@section('page-title', $dossier->titre)
@section('page-actions')
    <a href="{{ route('dossiers.modifier', $dossier) }}"
        class="text-sm border border-gray-200 px-3 py-1.5 rounded-lg text-gray-600 hover:bg-gray-50">Modifier</a>
@endsection

@section('content')

    {{-- ⭐⭐⭐ SECTION DES ACTIONS RAPIDES (À AJOUTER ICI, AVANT LA GRILLE) ⭐⭐⭐ --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5 mb-6">
        <h3 class="font-semibold text-gray-700 mb-4">Actions rapides</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Affecter un agent (visible uniquement pour admin et responsable) --}}
            @if(auth()->user()->estAdministrateur() || auth()->user()->estResponsable())
                <div>
                    <form method="POST" action="{{ route('dossiers.affecter-agent', $dossier) }}"
                        class="flex flex-col sm:flex-row gap-3 items-start sm:items-end">
                        @csrf
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Affecter un agent</label>
                            <select name="agent_id"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                                required>
                                <option value="">-- Choisir un agent --</option>
                                @if(isset($agents) && $agents->count() > 0)
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}" {{ $dossier->agent_id == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->nom_complet }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Aucun agent disponible</option>
                                @endif
                            </select>
                            </select>
                        </div>
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">
                            <i class="bi bi-person-check"></i> Affecter
                        </button>
                    </form>
                </div>
            @endif

            {{-- Agent actuel --}}
            <div>
                <p class="text-xs text-gray-400 uppercase mb-1">Agent responsable</p>
                <p class="font-medium text-gray-800">
                    @if($dossier->agent)
                        {{ $dossier->agent->nom_complet }}
                    @else
                        <span class="text-yellow-600">Non affecté</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Colonne gauche: infos + statut ──────────────────────── --}}
        <div class="space-y-4">
            {{-- Infos dossier --}}
            <div class="bg-white rounded-xl border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-700 mb-4">Informations</h3>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-gray-400 uppercase">Client</dt>
                        <dd class="font-medium mt-0.5">
                            <a href="{{ route('clients.show', $dossier->client) }}" class="text-blue-600 hover:underline">
                                {{ $dossier->client->nom_complet }}
                            </a>
                            <span class="text-gray-400 font-mono text-xs ml-1">({{ $dossier->client->cin }})</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 uppercase">Agent assigné</dt>
                        <dd class="font-medium mt-0.5">{{ $dossier->agent?->nom_complet ?? 'Non assigné' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 uppercase">Ouvert par</dt>
                        <dd class="font-medium mt-0.5">{{ $dossier->ouvertPar?->nom_complet ?? '–' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 uppercase">Créé le</dt>
                        <dd class="font-medium mt-0.5">{{ $dossier->created_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                    @if($dossier->description)
                        <div>
                            <dt class="text-xs text-gray-400 uppercase">Description</dt>
                            <dd class="mt-0.5 text-gray-600">{{ $dossier->description }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Changement de statut --}}
            @if(auth()->user()->estAdministrateur() || auth()->user()->estResponsable())
                <div class="bg-white rounded-xl border border-gray-100 p-5">
                    <h3 class="font-semibold text-gray-700 mb-3">Changer le statut</h3>
                    <div class="space-y-1.5">
                        @foreach(\App\Models\Dossier::STATUTS as $key => $meta)
                            <form method="POST" action="{{ route('dossiers.update-statut', $dossier) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="statut" value="{{ $key }}">
                                <button type="submit"
                                    class="w-full text-left px-3 py-2 rounded-lg text-sm transition-colors
                                               {{ $dossier->statut === $key ? 'badge-' . $key . ' font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                                    @if($dossier->statut === $key) ● @else ○ @endif
                                    {{ $meta['label'] }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Statut actuel :</p>
                    <span class="mt-2 inline-block px-3 py-1 rounded-full text-sm font-medium badge-{{ $dossier->statut }}">
                        {{ $dossier->statut_label }}
                    </span>
                </div>
            @endif
        </div>

        {{-- ── Colonne droite: demandes + documents ───────────────── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Demandes --}}
            <div class="bg-white rounded-xl border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-700">Demandes ({{ $dossier->demandes->count() }})</h3>
                    <a href="{{ route('demandes.create', ['dossier_id' => $dossier->id]) }}"
                        class="text-xs bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg hover:bg-blue-100">
                        + Demande
                    </a>
                </div>
                @forelse($dossier->demandes as $demande)
                    <div class="border border-gray-100 rounded-lg p-4 mb-3 last:mb-0">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-0.5 rounded">
                                {{ \App\Http\Controllers\DemandeController::TYPES[$demande->type_demande] ?? $demande->type_demande }}
                            </span>
                            @if($demande->montant)
                                <span class="text-sm font-semibold text-gray-700">{{ number_format($demande->montant, 2) }}
                                    MAD</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mt-2">{{ $demande->description }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $demande->created_at->format('d/m/Y') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-4">Aucune demande enregistrée</p>
                @endforelse
            </div>

            {{-- Documents --}}
            <div class="bg-white rounded-xl border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-700">Documents archivés ({{ $dossier->documents->count() }})</h3>
                </div>

                {{-- Upload --}}
                <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data"
                    class="mb-4 p-4 bg-gray-50 rounded-lg">
                    @csrf
                    <input type="hidden" name="dossier_id" value="{{ $dossier->id }}">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Type de document</label>
                            <select name="type_document" required
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none">
                                @foreach(\App\Http\Controllers\DocumentController::TYPES as $k => $l)
                                    <option value="{{ $k }}">{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Fichier (PDF / Image, max
                                10MB)</label>
                            <input type="file" name="fichier" required accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                    </div>
                    <button type="submit"
                        class="mt-3 text-sm bg-[#0f2744] text-white px-4 py-1.5 rounded-lg hover:bg-[#1a4a8a]">
                        Archiver
                    </button>
                </form>

                {{-- Liste documents --}}
                @forelse($dossier->documents as $doc)
                    <div class="flex items-center justify-between py-2 border-t border-gray-50 first:border-t-0">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-lg {{ $doc->isPdf() ? 'bg-red-50' : 'bg-blue-50' }} flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 {{ $doc->isPdf() ? 'text-red-500' : 'text-blue-500' }}" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ $doc->nom_fichier }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ \App\Http\Controllers\DocumentController::TYPES[$doc->type_document] ?? $doc->type_document }}
                                    · {{ $doc->taille_formatee }}
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('dossiers.pdf', $dossier) }}"
                                class="text-sm bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700">
                                <i class="bi bi-file-pdf"></i> Exporter PDF
                            </a>

                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('documents.download', $doc) }}"
                                class="text-xs text-blue-600 hover:underline">Télécharger</a>
                            <form method="POST" action="{{ route('documents.destroy', $doc) }}"
                                onsubmit="return confirm('Supprimer ce document ?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-500 hover:underline">Supprimer</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-2">Aucun document archivé</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
