<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicule extends Model
{
    use HasFactory;

    protected $fillable = [
        'immatriculation',
        'marque',
        'modele',
        'annee',
        'disponibilite',
    ];

     public function ordresMissions()
    {
        return $this->hasMany(OrdreMission::class);
    }

}
