<?php

namespace App\Repositories\Statistics;

use App\Models\Conge;
use Illuminate\Support\Facades\Auth;

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

    public function getStatsForCurrentUser(): array
    {
        $user = Auth::user();

        return [
            'en_attente' => Conge::where('statut', 'en_attente')                
                ->where('employe_id', $user->employe->id)
                ->count(),

            'approuve' => Conge::where('statut', 'approuve')
                ->where('employe_id', $user->employe->id)
                ->count(),

            'refuse' => Conge::where('statut', 'refuse')
                ->where('employe_id', $user->employe->id)
                ->count(),
        ];
    }
}
