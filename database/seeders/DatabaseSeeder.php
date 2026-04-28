<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'nom'     => 'Admin',           // ✅ nom (pas name)
            'prenom'  => 'Saham',           // ✅ prenom
            'email'   => 'admin@saham.ma',
            'password' => Hash::make('password'),
            'role'    => 'administrateur',
        ]);

        // Responsable
        User::create([
            'nom'     => 'Responsable',
            'prenom'  => 'Agence',
            'email'   => 'responsable@saham.ma',
            'password' => Hash::make('password'),
            'role'    => 'responsable',
        ]);

        // Agents
        $agents = [
            ['Karim', 'Alaoui', 'karim@saham.ma'],
            ['Fatima', 'Benali', 'fatima@saham.ma']
        ];
        
        foreach ($agents as [$nom, $prenom, $email]) {
            User::create([
                'nom'     => $nom,
                'prenom'  => $prenom,
                'email'   => $email,
                'password' => Hash::make('password'),
                'role'    => 'agent',
            ]);
        }
    }
}