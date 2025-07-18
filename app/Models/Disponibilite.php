<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disponibilite extends Model
{
    use HasFactory;

    protected $fillable = [
        'employe_id',
        'date_demande',
        'date_debut',
        'date_fin',
        'nombre_jours',
        'avec_solde',
        'statut',
        'motif',
        'piece_jointe',
        'fiche_disponibilite_pdf',
        'commentaire',
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }
}
