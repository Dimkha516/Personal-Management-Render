<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeConge extends Model
{
    protected $table = 'types_conges'; // <-- indique ici le nom réel de la table

    protected $fillable = [
        'libelle',
        'jours_par_defaut'
    ];

    public function conges()
    {
        return $this->hasMany(Conge::class, 'type_conge_id');
    }
}
