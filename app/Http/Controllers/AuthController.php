<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Afficher le formulaire de connexion
    public function showLogin()
    {
        // Si déjà connecté, rediriger vers le tableau de bord
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    // Traiter la connexion
    public function login(Request $request)
    {
        $request->validate([
            'email'       => 'required|email',
            'password'    => 'required|min:6',
        ], [
            'email.required'    => 'L\'adresse email est obligatoire.',
            'email.email'       => 'L\'adresse email n\'est pas valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min'      => 'Le mot de passe doit contenir au moins 6 caractères.',
        ]);

        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
            'actif'    => true,
        ];

        if (Auth::attempt($credentials, $request->boolean('se_souvenir'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $nomComplet = $user->prenom . ' ' . $user->nom;

            // ✅ REDIRECTION SELON LE RÔLE
            if ($user->estAdministrateur()) {
                return redirect()->route('dashboard')
                    ->with('succes', 'Bienvenue Administrateur, ' . $nomComplet . ' !');
            } elseif ($user->estResponsable()) {
                return redirect()->route('dashboard')
                    ->with('succes', 'Bienvenue Responsable, ' . $nomComplet . ' !');
            } elseif ($user->estAgent()) {
                return redirect()->route('dashboard')
                    ->with('succes', 'Bienvenue Agent, ' . $nomComplet . ' !');
            }

            // Redirection par défaut (au cas où)
            return redirect()->route('dashboard')
                ->with('succes', 'Bienvenue, ' . $nomComplet . ' !');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email ou mot de passe incorrect, ou compte désactivé.']);
    }

    // Déconnexion
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('succes', 'Vous êtes déconnecté avec succès.');
    }
}
