<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeDocument extends Model
{
    use HasFactory;

    protected $table = 'types_documents';
    protected $fillable = ['type', 'description'];

    public function documents() {
        return $this->hasMany(Document::class);
    }
}
