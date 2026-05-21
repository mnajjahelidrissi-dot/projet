<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;  // ← AJOUTER CETTE LIGNE
use Illuminate\Support\Str;

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
    /// pour récupérer les informations de l'utilisateur connecté
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    }



    /**
     * Envoyer le lien de réinitialisation par email
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:utilisateurs,email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Un lien de réinitialisation a été envoyé à votre adresse email.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Impossible d\'envoyer le lien de réinitialisation.'
        ], 400);
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:utilisateurs,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Utilisateur $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                // Supprimer tous les tokens existants (déconnecter l'utilisateur partout)
                $user->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Mot de passe réinitialisé avec succès. Vous pouvez maintenant vous connecter.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Le lien de réinitialisation est invalide ou a expiré.'
        ], 400);
    }
}
