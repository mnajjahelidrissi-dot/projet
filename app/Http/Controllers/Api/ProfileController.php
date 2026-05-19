<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * GET /api/profile
     * Récupère les informations de l'utilisateur connecté.
     */
    public function show(Request $request)
    {
        return response()->json([
            'success' => true,
            'data'    => $request->user()
        ], 200);
    }

    /**
     * PUT/PATCH /api/profile
     * Met à jour les informations personnelles et/ou le mot de passe.
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();

        // Mise à jour des informations de base transmises
        $user->nom       = $request->nom;
        $user->prenom    = $request->prenom;
        $user->email     = $request->email;
        $user->telephone = $request->telephone;

        // Si le mot de passe fait partie de la requête et est rempli, on le hache
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Si l'adresse email a changé, on réinitialise sa date de vérification
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès.',
            'data'    => $user
        ], 200);
    }

    /**
     * DELETE /api/profile
     * Révoque les tokens d'accès et supprime définitivement le compte utilisateur.
     */
    public function destroy(Request $request)
    {
        // Validation stricte du mot de passe actuel avant destruction
        $request->validate([
            'password' => ['required', 'current_password'],
        ], [
            'password.current_password' => 'Le mot de passe fourni est incorrect.'
        ]);

        $user = $request->user();

        // Révocation de tous les tokens Sanctum émis pour cet utilisateur (Déconnexion forcée)
        $user->tokens()->delete();

        // Suppression de l'enregistrement en base de données
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Votre compte a été supprimé définitivement de nos serveurs.'
        ], 200);
    }
}
