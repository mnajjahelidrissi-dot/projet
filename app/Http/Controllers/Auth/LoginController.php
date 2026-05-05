<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = \App\Models\Utilisateur::where('email', $request->email)
            ->where('actif', true)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password) || ! $user->actif) {
    throw ValidationException::withMessages([
        'email' => ['Les identifiants fournis sont incorrects.'],
    ]);
    }

        Auth::login($user);
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended('/tableau-de-bord');
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
