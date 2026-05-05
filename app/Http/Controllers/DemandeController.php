<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Dossier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemandeController extends Controller
{
    public const TYPES = [
        'ouverture_compte' => 'Ouverture de compte',
        'demande_carte'    => 'Demande de carte',
        'demande_credit'   => 'Demande de crédit',
        'reclamation'      => 'Réclamation',
        'autre'            => 'Autre',
    ];

    public function index(Request $request)
    {
        $query = Demande::with('dossier.client');

        if ($request->filled('type')) {
            $query->where('type_demande', $request->type);
        }

        $demandes = $query->latest()->paginate(15)->withQueryString();
        $types    = self::TYPES;

        return view('demandes.index', compact('demandes', 'types'));
    }

    public function create(Request $request)
    {
        $dossiers = Dossier::with('client')->whereIn('statut', ['en_attente', 'en_cours'])->get();
        $types    = self::TYPES;
        $dossierSelectionne = $request->dossier_id
            ? Dossier::find($request->dossier_id)
            : null;

        return view('demandes.create', compact('dossiers', 'types', 'dossierSelectionne'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'dossier_id'   => 'required|exists:dossiers,id',
            'type_demande' => 'required|in:' . implode(',', array_keys(self::TYPES)),
            'description'  => 'required|string',
            'montant'      => 'nullable|numeric|min:0',
        ]);

        $data['cree_par'] = Auth::id();

        Demande::create($data);

        return redirect()->route('dossiers.show', $data['dossier_id'])->with('success', 'Demande enregistrée.');
    }

    public function show(Demande $demande)
    {
        $demande->load('dossier.client');
        return view('demandes.show', compact('demande'));
    }

    public function destroy(Demande $demande)
    {
        $dossier_id = $demande->dossier_id;
        $demande->delete();
        return redirect()->route('dossiers.show', $dossier_id)->with('success', 'Demande supprimée.');
    }
}
