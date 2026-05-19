<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public const TYPES = [
        'cin'           => 'CIN',
        'domicile'      => 'Justificatif de domicile',
        'contrat'       => 'Contrat',
        'releve_compte' => 'Relevé de compte',
        'autre'         => 'Autre',
    ];

    /**
     * GET /api/documents
     * Liste des documents archivés, filtrable par dossier_id.
     */
    public function index(Request $request)
    {
        $query = Document::with('dossier.client');

        if ($request->filled('dossier_id')) {
            $query->where('dossier_id', $request->dossier_id);
        }

        $documents = $query->latest()->paginate(20)->withQueryString();

        return response()->json([
            'success' => true,
            'data'    => $documents,
            'metadata' => [
                'types_autorises' => self::TYPES
            ]
        ], 200);
    }

    /**
     * POST /api/documents
     * Upload et archivage d'un document.
     * Note Front : Envoyer les données via un objet `FormData`.
     */
    public function store(Request $request)
    {
        $request->validate([
            'dossier_id'    => 'required|exists:dossiers,id',
            'type_document' => 'required|in:' . implode(',', array_keys(self::TYPES)),
            // Correction de la cohérence de la règle max (10240 Ko = 10 Mo)
            'fichier'       => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ], [
            'fichier.max'      => 'Le fichier ne doit pas dépasser 10 Mo.',
            'fichier.mimes'    => 'Le fichier doit être au format PDF, JPG, JPEG ou PNG.',
            'fichier.required' => 'Veuillez sélectionner un fichier.',
        ]);

        // Stockage du fichier sur le disque privé
        $path = $request->file('fichier')->store('documents', 'private');

        $document = Document::create([
            'dossier_id'    => $request->dossier_id,
            'type_document' => $request->type_document,
            'nom_fichier'   => $request->file('fichier')->getClientOriginalName(),
            'chemin'        => $path,
            'taille'        => $request->file('fichier')->getSize(),
            'mime_type'     => $request->file('fichier')->getMimeType(),
            'uploade_par'   => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document archivé avec succès.',
            'data'    => $document
        ], 201);
    }

    /**
     * GET /api/documents/{id}/download
     * Téléchargement sécurisé du fichier binaire.
     */
    public function download(Document $document)
    {
        if (!Storage::disk('private')->exists($document->chemin)) {
            return response()->json([
                'success' => false,
                'message' => 'Fichier physique introuvable sur le serveur.'
            ], 404);
        }

        return response()->download(
            Storage::disk('private')->path($document->chemin),
            $document->nom_fichier
        );
    }

    /**
     * DELETE /api/documents/{id}
     * Suppression du fichier sur le disque et de son enregistrement en BDD.
     */
    public function destroy(Document $document)
    {
        // On supprime d'abord le fichier sur le disque s'il existe
        if (Storage::disk('private')->exists($document->chemin)) {
            Storage::disk('private')->delete($document->chemin);
        }

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document supprimé avec succès.'
        ], 200);
    }
}
