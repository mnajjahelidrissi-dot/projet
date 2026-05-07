<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Utilisateur;

class UtilisateurController extends Controller
{
    public function index()
    {
        $utilisateurs= Utilisateur::latest()->paginate(20);
        return view('utilisateurs.index', compact('utilisateurs'));
    }

    public function create()
    {
        return view('utilisateurs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'     => 'required|string|max:100',
            'prenom'  => 'required|string|max:100',
            'email' => 'required|email|unique:utilisateurs,email,' . $request->id,
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:administrateur,agent,responsable',
        ]);

        $data['password'] = Hash::make($data['password']);

        Utilisateur::create($data);
        $user = Utilisateur::where('email', $data['email'])->first();
        Auth::login($user);

        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur créé.');
    }

    public function edit(Utilisateur $utilisateur)
    {
        return view('utilisateurs.edit', compact('utilisateur'));
    }

    public function update(Request $request, Utilisateur $utilisateur)
    {
        $data = $request->validate([
            'nom'  => 'required|string|max:100',
            'prenom'  => 'required|string|max:100',
            'email' => 'required|email|unique:utilisateurs,email,' . $utilisateur->id,
            'role'  => 'required|in:administrateur,agent,responsable',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        $utilisateur->update($data);

        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur mis à jour.');
    }

    public function destroy(Utilisateur $utilisateur)
    {
        if ($utilisateur->id=== Auth::id()) {
            return back()->withErrors('Vous ne pouvez pas vous supprimer.');
        }
        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur supprimé.');
    }
 // Dans app/Http/Controllers/UtilisateurController.php

public function toggleStatus(Utilisateur $utilisateur)
{
    // ✅ AJOUTER : Vérifier que l'utilisateur est administrateur
    if (auth()->user()->role !== 'administrateur') {
        return back()->with('error', 'Action non autorisée. Vous devez être administrateur.');
    }

    // Empêcher l'utilisateur de se désactiver lui-même
    if (auth()->id() === $utilisateur->id) {
        return back()->with('error', 'Vous ne pouvez pas modifier votre propre statut.');
    }

    // Inversion du statut
    $utilisateur->update([
        'actif' => !$utilisateur->actif
    ]);

    $message = $utilisateur->actif ? 'Utilisateur activé avec succès.' : 'Utilisateur désactivé avec succès.';
    return back()->with('success', $message);
}
}
