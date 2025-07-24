<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['nom', 'fichier', 'description', 'employe_id'];

    public function employe() {
        return $this->belongsTo(Employe::class);
    }

    public function typeDocument()
    {
        return $this->belongsTo(TypeDocument::class, 'type_document_id');
        // return $this->belongsTo(TypeAgent::class, 'type_agent_id');
    }
}
