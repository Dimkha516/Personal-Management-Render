<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chauffeur extends Model
{
    use HasFactory;

     protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'disponible',
    ];

    public function ordresMissions()
    {
        return $this->hasMany(OrdreMission::class);
    }
}
