<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FraisMission extends Model
{
    use HasFactory;

    protected $fillable = [
        'ordre_mission_id',
        'libelle',
        'montant',
        'payable',
    ];

    public function ordreMission()
    {
        return $this->belongsTo(OrdreMission::class);
    }
}
