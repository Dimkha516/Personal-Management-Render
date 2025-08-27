<?php

namespace Database\Seeders;

use App\Models\OrdreMission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrdreMissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrdreMission::create([
            'demandeur_id' => 1,
            'destination' => 'Dakar - Thiès',
            'kilometrage' => 70,
            'qte_carburant' => 9.1,
            'vehicule_id' => 1,
            'chauffeur_id' => 1,
            'total_frais' => 5000,
            'numero_identification' => 'OM-2025-001',
            'date_depart' => '2025-08-20',
            'date_debut' => '2025-08-21',
            'date_fin' => '2025-08-23',
            'nb_jours' => 3,
            'statut' => 'approuve',
            'carburant_valide' => true,
        ]);

        OrdreMission::create([
            'demandeur_id' => 2,
            'destination' => 'Dakar - Kaolack',
            'kilometrage' => 190,
            'qte_carburant' => 24.7,
            'vehicule_id' => 2,
            'chauffeur_id' => 2,
            'total_frais' => 15000,
            'numero_identification' => 'OM-2025-002',
            'date_depart' => '2025-08-22',
            'date_debut' => '2025-08-22',
            'date_fin' => '2025-08-25',
            'nb_jours' => 4,
            'statut' => 'en_attente',
            'carburant_valide' => false,
        ]);

        OrdreMission::create([
            'demandeur_id' => 3,
            'destination' => 'Dakar - Saint-Louis',
            'kilometrage' => 270,
            'qte_carburant' => 35.1,
            'vehicule_id' => 3,
            'chauffeur_id' => 3,
            'total_frais' => 20000,
            'numero_identification' => 'OM-2025-003',
            'date_depart' => '2025-08-25',
            'date_debut' => '2025-08-25',
            'date_fin' => '2025-08-29',
            'nb_jours' => 5,
            'statut' => 'rejete',
            'carburant_valide' => false,
        ]);
    }
}
