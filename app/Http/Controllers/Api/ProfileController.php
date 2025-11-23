<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    
    /**
     * Afficher le profil public d'un utilisateur par username
     */
    public function show($username)
    {
        $user = User::where('username', $username)
            ->with('reviews')
            ->firstOrFail();

        return response()->json([
            'user' => $user,
        ], 200);
    }

    /**
     * Mettre à jour le profil de l'utilisateur connecté
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:users,username,' . $user->id . '|alpha_dash',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|url',
        ]);

        $user->update($request->only(['name', 'username', 'bio', 'avatar']));

        return response()->json([
            'message' => 'Profil mis à jour',
            'user' => $user,
        ], 200);
    }

}
