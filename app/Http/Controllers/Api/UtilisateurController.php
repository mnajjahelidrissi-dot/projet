<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UtilisateurController extends Controller
{
    /**
     * GET /api/utilisateurs
     * Liste paginée des utilisateurs.
     */
    public function index()
    {
        $utilisateurs = Utilisateur::latest()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $utilisateurs
        ], 200);
    }

    /**
     * POST /api/utilisateurs
     * Création d'un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'      => 'required|string|max:100',
            'prenom'   => 'required|string|max:100',
            'email'    => 'required|email|unique:utilisateurs,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:administrateur,agent,responsable',
        ]);

        $data['password'] = Hash::make($data['password']);

        $utilisateur = Utilisateur::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur créé avec succès.',
            'data' => $utilisateur
        ], 201); // 201 Created
    }

    /**
     * GET /api/utilisateurs/{id}
     * Afficher les détails d'un utilisateur spécifique.
     */
    public function show(Utilisateur $utilisateur)
    {
        return response()->json([
            'success' => true,
            'data' => $utilisateur
        ], 200);
    }

    /**
     * PUT/PATCH /api/utilisateurs/{id}
     * Mise à jour d'un utilisateur.
     */
    public function update(Request $request, Utilisateur $utilisateur)
    {
        $data = $request->validate([
            'nom'    => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email'  => 'required|email|unique:utilisateurs,email,' . $utilisateur->id,
            'role'   => 'required|in:administrateur,agent,responsable',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        $utilisateur->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur mis à jour avec succès.',
            'data' => $utilisateur
        ], 200);
    }

    /**
     * DELETE /api/utilisateurs/{id}
     * Suppression d'un utilisateur (avec vérifications de sécurité).
     */
    public function destroy(Request $request, Utilisateur $utilisateur)
    {
        $currentUser = $request->user();

        // Vérifier que l'utilisateur ne se supprime pas lui-même
        if ($utilisateur->id === $currentUser->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas supprimer votre propre compte.'
            ], 403); // 403 Forbidden
        }

        // Vérifier qu'il n'est pas le dernier admin
        if ($utilisateur->role === 'administrateur') {
            $adminCount = Utilisateur::where('role', 'administrateur')->count();

            if ($adminCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer le seul administrateur restant.'
                ], 403);
            }
        }

        $utilisateur->delete();

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur supprimé avec succès.'
        ], 200);
    }

    /**
     * PATCH /api/utilisateurs/{id}/toggle-status
     * Activation/Désactivation d'un compte utilisateur.
     */
    public function toggleStatus(Request $request, Utilisateur $utilisateur)
    {
        $currentUser = $request->user();

        // Vérifier que l'utilisateur qui fait la demande est bien administrateur
        if ($currentUser->role !== 'administrateur') {
            return response()->json([
                'success' => false,
                'message' => 'Action non autorisée. Vous devez être administrateur.'
            ], 403);
        }

        // Empêcher l'utilisateur de se désactiver lui-même
        if ($currentUser->id === $utilisateur->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas modifier votre propre statut.'
            ], 403);
        }

        // Inversion du statut
        $utilisateur->update([
            'actif' => !$utilisateur->actif
        ]);

        $statusMessage = $utilisateur->actif ? 'activé' : 'désactivé';

        return response()->json([
            'success' => true,
            'message' => "Utilisateur {$statusMessage} avec succès.",
            'data' => [
                'id' => $utilisateur->id,
                'actif' => $utilisateur->actif
            ]
        ], 200);
    }
}
