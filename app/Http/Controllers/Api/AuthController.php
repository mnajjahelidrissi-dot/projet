<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Connexion de l'utilisateur et génération du token.
     */
    public function login(Request $request)
    {
        // 1. Validation stricte des données entrantes
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // 2. Recherche de l'utilisateur actif
        $user = Utilisateur::where('email', $credentials['email'])
            ->where('actif', true)
            ->first();

        // 3. Vérification de l'existence et du mot de passe
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email ou mot de passe incorrect.'
            ], 401); // 401 Unauthorized
        }
        Auth::login($user);

        $user->tokens()->delete();
        // 4. Génération du token d'accès personnalisé
        // Remplacement de 'auth_token' par un nom dynamique ou plus explicite
        $token = $user->createToken('API Token of ' . $user->nom)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie.',
            'user' => [
                'id'     => $user->id,
                'nom'    => $user->nom,
                'prenom' => $user->prenom,
                'email'  => $user->email,
                'role'   => $user->role,
            ],
            'token' => $token
        ], 200); // 200 OK
    }

    /**
     * Déconnexion de l'utilisateur (Révocation du token actuel).
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    }
}
