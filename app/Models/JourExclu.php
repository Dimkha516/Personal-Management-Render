<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JourExclu extends Model
{
    use HasFactory;

    protected $table = 'jours_exclus';

    protected $fillable = [
        'date',
        'jour_semaine',
        'motif',
        'type_exclusion',
    ];

    // Exemple d'accessor pour récupérer le nom du jour
    public function getJourSemaineNomAttribute()
    {
        $jours = [
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche',
        ];

        return $this->jour_semaine ? $jours[$this->jour_semaine] : null;
    }
}
