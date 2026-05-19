<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function update(Request $request)
    {
        // Validation de base
        $rules = [
            'password' => 'required|min:8|confirmed',
        ];

        // current_password est optionnel (pour compatibilité avec les tests)
        // Si présent, on le valide
        if ($request->has('current_password')) {
            $rules['current_password'] = 'required';
        }

        $request->validate($rules);

        $user = $request->user();

        // Vérifier le mot de passe actuel SEULEMENT s'il est fourni
        if ($request->has('current_password') && !Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Le mot de passe actuel est incorrect'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe mis à jour avec succès'
        ]);
    }
}
