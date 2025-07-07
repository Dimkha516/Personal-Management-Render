<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'chef_service_id'];

    public function employes()
    {
        return $this->hasMany(Employe::class);
    }

    public function chef()
    {
        return $this->belongsTo(Employe::class, 'chef_service_id');
    }
}
