<?php

namespace App\Models;

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumToken;

class PersonalAccessToken extends SanctumToken
{
    // On garde uniquement ce qu'on veut contrôler
    protected $casts = [
        'last_used_at' => 'datetime',
        'abilities' => 'array',
    ];
}
