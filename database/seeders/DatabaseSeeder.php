<?php

namespace Database\Seeders;

use App\Models\Championnat;
use App\Models\Equipe;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrateur',
                'email' => 'admin@example.com',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
            ]
        );

        $visitor = User::updateOrCreate(
            ['username' => 'visiteur'],
            [
                'name' => 'Visiteur',
                'email' => 'visiteur@example.com',
                'role' => 'visiteur',
                'password' => Hash::make('visiteur123'),
            ]
        );

        // Pré-remplissage de données de démonstration pour démarrer rapidement
        $ligue1 = Championnat::firstOrCreate(['nom' => 'Ligue 1']);
        $premierLeague = Championnat::firstOrCreate(['nom' => 'Premier League']);

        $equipes = [
            ['nom' => 'Paris SG', 'championnat_id' => $ligue1->id],
            ['nom' => 'Marseille', 'championnat_id' => $ligue1->id],
            ['nom' => 'Lyon', 'championnat_id' => $ligue1->id],
            ['nom' => 'Manchester City', 'championnat_id' => $premierLeague->id],
            ['nom' => 'Liverpool', 'championnat_id' => $premierLeague->id],
            ['nom' => 'Arsenal', 'championnat_id' => $premierLeague->id],
        ];

        foreach ($equipes as $data) {
            Equipe::firstOrCreate($data);
        }
    }
}
