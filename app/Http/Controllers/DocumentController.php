<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Dossier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
class DocumentController extends Controller
{
    public const TYPES = [
        'cin'           => 'CIN',
        'domicile'      => 'Justificatif de domicile',
        'contrat'       => 'Contrat',
        'releve_compte' => 'Relevé de compte',
        'autre'         => 'Autre',
    ];

    public function index(Request $request)
    {
        $query = Document::with('dossier.client');

        if ($request->filled('dossier_id')) {
            $query->where('dossier_id', $request->dossier_id);
        }

        $documents = $query->latest()->paginate(20)->withQueryString();
        $types     = self::TYPES;

        return view('documents.index', compact('documents', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dossier_id'   => 'required|exists:dossiers,id',
            'type_document' => 'required|in:' . implode(',', array_keys(self::TYPES)),
            'fichier'      => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $path = $request->file('fichier')->store('documents', 'private');

        Document::create([
            'dossier_id'    => $request->dossier_id,
            'type_document' => $request->type_document,
            'nom_fichier'   => $request->file('fichier')->getClientOriginalName(),
            'chemin'        => $path,
            'taille'        => $request->file('fichier')->getSize(),
            'mime_type'     => $request->file('fichier')->getMimeType(),
            'uploade_par'   => Auth::id(),
        ]);

        return back()->with('success', 'Document archivé avec succès.');
    }

    public function download(Document $document)
    {
        if (!Storage::disk('private')->exists($document->chemin)) {
            abort(404, 'Fichier introuvable.');
        }

        return response()->download(
            Storage::disk('private')->path($document->chemin),
            $document->nom_fichier
        );
    }

    public function destroy(Document $document)
    {
        Storage::disk('private')->delete($document->chemin);
        $document->delete();

        return back()->with('success', 'Document supprimé.');
    }
}
