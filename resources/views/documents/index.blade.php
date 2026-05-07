{{-- FICHIER: resources/views/documents/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Documents')
@section('page-title', 'Archivage des documents')

@section('content')
<div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr class="text-left text-xs text-gray-500 uppercase tracking-wide">
                <th class="px-4 py-3">Document</th>
                <th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Dossier</th>
                <th class="px-4 py-3">Client</th>
                <th class="px-4 py-3">Taille</th>
                <th class="px-4 py-3">Date</th>
                <th class="px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($documents as $doc)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded {{ $doc->isPdf() ? 'bg-red-50' : 'bg-blue-50' }} flex items-center justify-center">
                            <span class="text-xs font-bold {{ $doc->isPdf() ? 'text-red-500' : 'text-blue-500' }}">
                                {{ $doc->isPdf() ? 'PDF' : 'IMG' }}
                            </span>
                        </div>
                        <span class="text-gray-700 max-w-[160px] truncate">{{ $doc->nom_fichier }}</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $types[$doc->type_document] ?? $doc->type_document }}</td>
                <td class="px-4 py-3">
                    <a href="{{ route('dossiers.show', $doc->dossier) }}" class="text-blue-600 hover:underline text-xs">
                        {{ $doc->dossier->titre }}
                    </a>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $doc->dossier->client->nom_complet }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $doc->taille_formatee }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $doc->created_at->format('d/m/Y') }}</td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('documents.download', $doc) }}" class="text-xs text-blue-600 hover:underline">Télécharger</a>
                        <form method="POST" action="{{ route('documents.destroy', $doc) }}" onsubmit="return confirm('Supprimer ?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:underline">Supprimer</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Aucun document archivé</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $documents->links() }}</div>
@endsection
