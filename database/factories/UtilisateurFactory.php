<?php

namespace Database\Factories;

use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Utilisateur>
 */
class UtilisateurFactory extends Factory
{
    // Correction : Lier explicitement au modèle Utilisateur
    protected $model = Utilisateur::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'nom'            => fake()->lastName(),
            'prenom'         => fake()->firstName(),
            'email'          => fake()->unique()->safeEmail(),
            'password'       => static::$password ??= Hash::make('password'),
            'role'           => 'agent', // Rôle par défaut
            'telephone'      => fake()->phoneNumber(),
            'actif'          => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'administrateur',
        ]);
    }
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

}
