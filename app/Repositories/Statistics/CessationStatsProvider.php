<?php

namespace App\Repositories\Statistics;

use App\Models\Cessation;
use Illuminate\Support\Facades\Auth;

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

    public function getStatsForCurrentUser(): array
    {
        $user = Auth::user();

        return [
            'en_attente' => Cessation::where('statut', 'en_attente')                
                ->where('employe_id', $user->employe->id)
                ->count(),

            'approuve' => Cessation::where('statut', 'approuve')
                ->where('employe_id', $user->employe->id)
                ->count(),

            'refuse' => Cessation::where('statut', 'refuse')
                ->where('employe_id', $user->employe->id)
                ->count(),
        ];
    }
}
