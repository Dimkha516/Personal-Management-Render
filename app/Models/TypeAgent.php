<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeAgent extends Model
{
    protected $table = 'types_agent';
    protected $fillable = ['name'];

    public function employes()
    {
        return $this->hasMany(Employe::class);
    }
}
