<?php
namespace App\Repositories\Statistics;

use App\Models\Disponibilite;
use Illuminate\Support\Facades\Auth;

class DisponibiliteStatsProvider{
    public function  getStats(): array {
        return [
            'en_attente' => Disponibilite::where('statut', 'en_attente')->count(),
            'approuve' => Disponibilite::where('statut', 'approuve')->count(),
            'refuse'  => Disponibilite::where('statut', 'refuse')->count(),
        ];   
    }

    public function getStatsForCurrentUser(): array
    {
        $user = Auth::user();

        return [
            'en_attente' => Disponibilite::where('statut', 'en_attente')                
                ->where('employe_id', $user->employe->id)
                ->count(),

            'approuve' => Disponibilite::where('statut', 'valide')
                ->where('employe_id', $user->employe->id)
                ->count(),

            'refuse' => Disponibilite::where('statut', 'refuse')
                ->where('employe_id', $user->employe->id)
                ->count(),
        ];
    }
}