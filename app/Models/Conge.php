<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conge extends Model
{   
    protected $table = 'conges';

    protected $fillable = [
        'employe_id',
        'type_conge_id',
        'date_demande',
        'date_debut',
        'date_fin',
        'nombre_jours',
        'statut',
        'commentaire',
        'piece_jointe',
        'note_pdf',
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class, 'employe_id');
    }

    public function typeConge(){
        return $this->belongsTo(TypeConge::class);
    }

    public function cessations(){
        return $this->hasMany(Cessation::class);
    }
}
