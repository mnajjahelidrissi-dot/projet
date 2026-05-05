<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Création des Utilisateurs (Admin, Responsable, Agent)
        DB::table('utilisateurs')->insert([
            [
                'nom' => 'Saham',
                'prenom' => 'Admin',
                'email' => 'admin@saham.ma',
                'password' => Hash::make('password'),
                'role' => 'administrateur',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Responsable',
                'prenom' => 'User',
                'email' => 'responsable@saham.ma',
                'password' => Hash::make('password'),
                'role' => 'responsable',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Agent',
                'prenom' => 'Test',
                'email' => 'agent@saham.ma',
                'password' => Hash::make('password'),
                'role' => 'agent',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 2. Création d'un Client
        DB::table('clients')->insert([
            'numero_client' => 'CLI-2026-0001',
            'nom' => 'Alami',
            'prenom' => 'Hassan',
            'cin' => 'AB123456',
            'telephone' => '0600112233',
            'email' => 'hassan.alami@example.com',
            'statut' => 'actif',
            'cree_par' => 1, // L'ID de l'admin créé au dessus
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Création d'un Dossier (lié au client 1)
        DB::table('dossiers')->insert([
            'client_id' => 1,
            'titre' => 'Dossier de test ',
            'description' => 'Description facultative',
            'statut' => 'en_attente',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // On ne remplit pas 'demandes' et 'documents' car tes migrations sont vides pour l'instant
    }
}
