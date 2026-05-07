@extends('layouts.app')
@section('title', 'Dossiers')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Liste des dossiers</h1>
    @if(auth()->user()->estAdministrateur() || auth()->user()->estResponsable())
    <a href="{{ route('dossiers.creer') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
        + Nouveau dossier
    </a>
    @endif
</div>

<!-- Filtres -->
<div class="bg-white rounded-xl border border-gray-100 p-4 mb-6">
    <form method="GET" action="{{ route('dossiers.index') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Statut</label>
            <select name="statut" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none">
                <option value="">Tous les statuts</option>
                @foreach(\App\Models\Dossier::STATUTS as $key => $meta)
                    <option value="{{ $key }}" {{ request('statut') === $key ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                @endforeach
            </select>
        </div>

        @if(auth()->user()->estAdministrateur() || auth()->user()->estResponsable())
        <div>
            <label class="block text-xs text-gray-500 mb-1">Agent</label>
            <select name="agent_id" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none">
                <option value="">Tous les agents</option>
                @foreach($agents as $agent)
                    <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>{{ $agent->nom_complet }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <button type="submit" class="px-4 py-2 bg-gray-100 rounded-lg text-sm hover:bg-gray-200">Filtrer</button>
        @if(request()->hasAny(['statut','agent_id']))
            <a href="{{ route('dossiers.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:underline">Effacer</a>
        @endif
    </form>
</div>

<!-- Tableau des dossiers -->
<div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Créé le</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($dossiers as $dossier)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $dossier->id }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    <a href="{{ route('dossiers.show', $dossier) }}" class="text-blue-600 hover:underline">
                        {{ $dossier->titre }}
                    </a>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $dossier->client->nom_complet ?? 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $dossier->agent->nom_complet ?? 'Non affecté' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs rounded-full badge-{{ $dossier->statut }}">
                        {{ $dossier->statut_label }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $dossier->created_at->format('d/m/Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ route('dossiers.show', $dossier) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                        Voir
                    </a>
                    @if(auth()->user()->estAdministrateur() || auth()->user()->estResponsable())
                    <a href="{{ route('dossiers.modifier', $dossier) }}" class="text-indigo-600 hover:text-indigo-900">
                        Modifier
                    </a>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                    Aucun dossier trouvé
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-4">
    {{ $dossiers->withQueryString()->links() }}
</div>
@endsection
