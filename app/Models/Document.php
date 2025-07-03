<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['nom', 'fichier', 'description', 'employe_id'];

    public function employe() {
        return $this->belongsTo(Employe::class);
    }
}
