<?php

namespace Database\Seeders;

use App\Models\Disponibilite;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DisponibilitesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $disponibilites = [
            [
                "employe_id" => 3,
                "date_demande" => "2020-01-01",
                "date_debut" => "2020-01-01",
                "date_fin" => "2020-01-10",
            ],
        ];
        foreach ($disponibilites as $dispo) {
            Disponibilite::create($dispo);
        }
    }
}
