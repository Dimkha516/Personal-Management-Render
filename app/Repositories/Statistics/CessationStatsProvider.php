<?php

namespace App\Repositories\Statistics;

use App\Models\Cessation;

class CessationStatsProvider
{
    public function getStats(): array
    {
        return [
            'total' => Cessation::count(),
            'en_attente' => Cessation::where('statut', 'en_attente')->count(),
            'valide' => Cessation::where('statut', 'valide')->count(),
            'rejete' => Cessation::where('statut', 'rejete')->count(),
        ];
    }
}
