<?php

namespace Database\Seeders;

use App\Models\FraisMission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FraisMissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Frais pour OM 1
        FraisMission::create([
            'ordre_mission_id' => 1,
            'libelle' => 'Hébergement',
            'montant' => 3000,
            'payable' => true,
        ]);

        FraisMission::create([
            'ordre_mission_id' => 1,
            'libelle' => 'Repas',
            'montant' => 2000,
            'payable' => true,
        ]);

        // Frais pour OM 2
        FraisMission::create([
            'ordre_mission_id' => 2,
            'libelle' => 'Hébergement',
            'montant' => 10000,
            'payable' => true,
        ]);

        // Frais pour OM 3
        FraisMission::create([
            'ordre_mission_id' => 3,
            'libelle' => 'Hébergement',
            'montant' => 12000,
            'payable' => false, // rejeté
        ]);
    }
}
