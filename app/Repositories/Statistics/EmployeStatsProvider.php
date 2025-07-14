<?php

namespace App\Repositories\Statistics;


use App\Models\Employe;

class EmployeStatsProvider
{
    public function getStats(): array
    {
        return [
            'total' => Employe::count(),
            'CDI' => Employe::where('type_contrat', 'CDI')->count(),
            'CDD' => Employe::where('type_contrat', 'cdd')->count(),
            'Hommes' => Employe::where('genre', 'Masculin')->count(),
            'Femmes' => Employe::where('genre', 'Féminin')->count(),
            // 'fonctionnaires' => Employe::where('type_contrat', 'fonctionnaire')->count(),
        ];
    }
}
