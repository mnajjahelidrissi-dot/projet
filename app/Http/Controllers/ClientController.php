<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nom', 'like', "%$s%")
                  ->orWhere('prenom', 'like', "%$s%")
                  ->orWhere('cin', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%");
            });
        }

        $clients = $query->latest()->paginate(15)->withQueryString();

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'         => 'required|string|max:100',
            'prenom'      => 'required|string|max:100',
            'cin'         => 'required|string|unique:clients,cin|max:20',
            'date_naissance' => 'required|date',
            'telephone'   => 'required|string|max:20',
            'email'       => 'nullable|email|max:150',
            'adresse'     => 'required|string|max:255',
            'ville'       => 'required|string|max:100',
        ]);

        Client::create($data);

        return redirect()->route('clients.index')->with('success', 'Client ajouté avec succès.');
    }

    public function show(Client $client)
    {
        $client->load(['dossiers.demandes', 'dossiers.agent']);
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'nom'         => 'required|string|max:100',
            'prenom'      => 'required|string|max:100',
            'cin'         => 'required|string|max:20|unique:clients,cin,' . $client->id,
            'date_naissance' => 'required|date',
            'telephone'   => 'required|string|max:20',
            'email'       => 'nullable|email|max:150',
            'adresse'     => 'required|string|max:255',
            'ville'       => 'required|string|max:100',
        ]);

        $client->update($data);

        return redirect()->route('clients.show', $client)->with('success', 'Client mis à jour.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client supprimé.');
    }

    public function search(Request $request)
    {
        $results = Client::where('nom', 'like', '%' . $request->q . '%')
            ->orWhere('cin', 'like', '%' . $request->q . '%')
            ->limit(10)
            ->get(['id', 'nom', 'prenom', 'cin']);

        return response()->json($results);
    }
}
