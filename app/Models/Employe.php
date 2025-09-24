<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employe extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'email',
        'adresse',
        'date_naiss',
        'lieu_naiss',
        'situation_matrimoniale',
        'date_prise_service',
        'date_fin_contrat',
        'date_dernier_demande_conge',
        'duree_contrat_mois',
        'genre',
        'type_contrat',
        'solde_conge_jours',
        'fonction_id',
        'service_id',
        'type_agent_id',
        'user_id'
    ];

    public function fonction()
    {
        return $this->belongsTo(Fonction::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function typeAgent()
    {
        return $this->belongsTo(TypeAgent::class, 'type_agent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function conges()
    {
        return $this->hasMany(Conge::class);
    }

    public function cessations()
    {
        return $this->hasMany(Cessation::class);
    }

    public function disponibilites()
    {
        return $this->hasMany(Disponibilite::class);
    }

    public function ordresMission()
    {
        return $this->hasMany(OrdreMission::class);
    }
}
