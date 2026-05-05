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
        $users = Utilisateur::latest()->paginate(20);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
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

        return redirect()->route('users.index')->with('success', 'Utilisateur créé.');
    }

    public function edit(Utilisateur $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, Utilisateur $user)
    {
        $data = $request->validate([
            'nom'  => 'required|string|max:100',
            'prenom'  => 'required|string|max:100',
            'email' => 'required|email|unique:utilisateurs,email,' . $user->id,
            'role'  => 'required|in:administrateur,agent,responsable',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour.');
    }

    public function destroy(Utilisateur $user)
    {
        if ($user->id=== Auth::id()) {
            return back()->withErrors('Vous ne pouvez pas vous supprimer.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé.');
    }
}
