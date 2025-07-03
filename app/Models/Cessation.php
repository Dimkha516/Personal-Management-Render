<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cessation extends Model
{
    protected $fillable = [
        'conge_id',
        'date_debut',
        'date_fin',
        'statut',
        'commentaire',
        'fiche_cessation_pdf',
        'nombre_jours'  
    ];

    public function conge()
    {
        return $this->belongsTo(Conge::class);
    }
}
