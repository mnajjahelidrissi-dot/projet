<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Demande;
use Illuminate\Http\Request;

class DemandeController extends Controller
{
    // Constante conservée pour centraliser la validation de l'API
    public const TYPES = [
        'ouverture_compte' => 'Ouverture de compte',
        'demande_carte'    => 'Demande de carte',
        'demande_credit'   => 'Demande de crédit',
        'reclamation'      => 'Réclamation',
        'autre'            => 'Autre',
    ];

    /**
     * GET /api/demandes
     * Liste des demandes avec filtrage par type et pagination.
     */
    public function index(Request $request)
    {
        $query = Demande::with('dossier.client');

        // Filtrage dynamique par type de demande
        if ($request->filled('type')) {
            $query->where('type_demande', $request->type);
        }

        $demandes = $query->latest()->paginate(15)->withQueryString();

        return response()->json([
            'success' => true,
            'data'    => $demandes,
            'metadata' => [
                'types_autorises' => self::TYPES // Permet au Front d'avoir la correspondance si besoin
            ]
        ], 200);
    }

    /**
     * POST /api/demandes
     * Enregistrement d'une nouvelle demande liée à un dossier.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'dossier_id'   => 'required|exists:dossiers,id',
            'type_demande' => 'required|in:' . implode(',', array_keys(self::TYPES)),
            'description'  => 'required|string',
            'montant'      => 'nullable|numeric|min:0',
        ]);

        // Assignation de l'ID de l'utilisateur authentifié via Sanctum
        $data['cree_par'] = $request->user()->id;

        $demande = Demande::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Demande enregistrée avec succès.',
            'data'    => $demande->load('dossier.client')
        ], 201); // 201 Created
    }

    /**
     * GET /api/demandes/{id}
     * Récupérer les détails d'une demande spécifique.
     */
    public function show(Demande $demande)
    {
        $demande->load('dossier.client');

        return response()->json([
            'success' => true,
            'data'    => $demande
        ], 200);
    }

    /**
     * DELETE /api/demandes/{id}
     * Supprimer une demande.
     */
    public function destroy(Demande $demande)
    {
        $demande->delete();

        return response()->json([
            'success' => true,
            'message' => 'Demande supprimée avec succès.'
        ], 200);
    }
}
