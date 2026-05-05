<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\Client;
use App\Models\Demande;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class DossierController extends Controller
{
    public function index(Request $request)
    {
        $query = Dossier::with(['client', 'agent']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        // Un agent ne voit que ses dossiers
        if (Auth::user()->role === 'agent') {
            $query->where('agent_id', Auth::id());
        }

        $dossiers = $query->latest()->paginate(15)->withQueryString();
        $agents   = Utilisateur::where('role', 'agent')->get();

        return view('dossiers.index', compact('dossiers', 'agents'));
    }

    public function create()
    {
        $clients = Client::orderBy('nom')->get();
        $agents  = Utilisateur::where('role', 'agent')->get();

        return view('dossiers.create', compact('clients', 'agents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'   => 'required|exists:clients,id',
            'agent_id'    => 'nullable|exists:utilisateurs,id',
            'titre'       => 'required|string|max:200',
            'description' => 'nullable|string',
        ]);

        $data['statut']   = 'en_attente';
        $data['ouvert_par'] = Auth::id();

        $dossier = Dossier::create($data);

        return redirect()->route('dossiers.show', $dossier)->with('success', 'Dossier créé.');
    }

    public function show(Dossier $dossier)
    {
        $dossier->load(['client', 'agent', 'demandes', 'documents', 'ouvertPar']);

        return view('dossiers.show', compact('dossier'));
    }

    public function edit(Dossier $dossier)
    {
        $clients = Client::orderBy('nom')->get();
        $agents  = Utilisateur::where('role', 'agent')->get();

        return view('dossiers.edit', compact('dossier', 'clients', 'agents'));
    }

    public function update(Request $request, Dossier $dossier)
    {
        $data = $request->validate([
            'client_id'   => 'required|exists:clients,id',
            'agent_id'    => 'nullable|exists:utilisateurs,id',
            'titre'       => 'required|string|max:200',
            'description' => 'nullable|string',
        ]);

        $dossier->update($data);

        return redirect()->route('dossiers.show', $dossier)->with('success', 'Dossier mis à jour.');
    }

    public function destroy(Dossier $dossier)
    {
        $dossier->delete();
        return redirect()->route('dossiers.index')->with('success', 'Dossier supprimé.');
    }

    public function updateStatut(Request $request, Dossier $dossier)
    {
        $request->validate([
            'statut' => 'required|in:en_attente,en_cours,valide,rejete',
        ]);

        $dossier->update(['statut' => $request->statut]);

        return back()->with('success', 'Statut mis à jour.');
    }
}
