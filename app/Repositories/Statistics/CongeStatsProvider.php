<?php

namespace App\Repositories\Statistics;

use App\Models\Conge;

class CongeStatsProvider
{
    public function getStats(): array
    {
        return [
            'total' => Conge::count(),
            'en_attente' => Conge::where('statut', 'en_attente')->count(),
            'approuve' => Conge::where('statut', 'approuve')->count(),
            'refuse'  => Conge::where('statut', 'refuse')->count(),
        ];
    }
}
