<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdreMission extends Model
{
    protected $table = 'ordres_missions';
    
    use HasFactory;

     protected $fillable = [
        'demandeur_id',
        'destination',
        'kilometrage',
        'qte_carburant',
        'vehicule_id',
        'chauffeur_id',
        'total_frais',
        'numero_identification',
        'date_depart',
        'date_debut',
        'date_fin',
        'nb_jours',
        'statut',
        'chef_service_validation',
        'motif_rejet',
        'carburant_valide',
    ];

    public function demandeur()
    {
        return $this->belongsTo(Employe::class, 'demandeur_id');
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class);
    }

    public function frais()
    {
        return $this->hasMany(FraisMission::class);
    }
}
